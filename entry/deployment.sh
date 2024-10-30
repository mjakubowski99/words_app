#!/bin/bash

RESET='\e[0m'
GREEN='\e[32m'
RED='\e[31m'

set -o allexport
source .env
set +o allexport

SERVER_USER=$(echo "$SSH_USER" | base64 --decode)
SERVER_HOST=$(echo "$SSH_HOST" | base64 --decode)
SERVER_PORT=$(echo "$SSH_PORT" | base64 --decode)
SERVER_PASS=$(echo "$SSH_PASSWORD" | base64 --decode)

if [[ -z "$SERVER_USER" || -z "$SERVER_HOST" || -z "$SERVER_PORT" || -z "$SERVER_PASS" ]]; then
  echo "Error: SERVER_USER, SERVER_HOST, SERVER_PORT, and SERVER_PASS must be set and base64 encoded in the .env file."
  exit 1
fi

if ! command -v sshpass &> /dev/null; then
  echo "Error: sshpass is not installed. Please install it and try again."
  exit 1
fi

# Definicja komend do uruchomienia na serwerze z użyciem heredoc
REMOTE_COMMANDS=$(cat <<EOF
set -e  # Przerwij wykonanie przy błędzie dowolnej komendy
echo -e '${GREEN}Połączono pomyślnie!${RESET}'

echo -e 'Pulling changes...'
cd /home/$SERVER_USER/voca_app \
&& {
    git checkout . \
    && git pull origin master \
    && echo -e '${GREEN}Changes pulled successfully!${RESET}'
} || {
    echo -e "${RED}Wystąpił błąd podczas pobierania zmian z repozytorium.${RESET}" 1>&2
    exit 1
}

sleep 1;


cd /home/$SERVER_USER/voca_app/entry/prod \

{
  docker compose build php \
  && docker compose down php \
  && docker compose up -d php \
  && docker compose exec php php artisan optimize:clear \
  && docker compose exec php php artisan optimize \
  && docker compose exec php php artisan migrate --force \
  && docker compose exec php composer open-api \
  && echo -e '${GREEN}Successfully deployed!${RESET}'
} || {
  echo -e "${RED}Deployment failed.${RESET}" 1>&2
}
EOF
)

sshpass -p "$SERVER_PASS" ssh -p "$SERVER_PORT" "$SERVER_USER@$SERVER_HOST" "$REMOTE_COMMANDS"



