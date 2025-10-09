<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizchlanie 🍕</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #ffd89b 0%, #ff8c42 100%);
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 140, 66, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 216, 155, 0.4) 0%, transparent 50%),
                linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ffd89b 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: #fffef7;
            border-radius: 30px;
            box-shadow: 0 25px 70px rgba(139, 69, 19, 0.4);
            max-width: 650px;
            width: 100%;
            padding: 45px;
            border: 4px solid #d4822f;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '🍝';
            position: absolute;
            font-size: 120px;
            opacity: 0.05;
            right: -20px;
            top: -20px;
            transform: rotate(-15deg);
        }

        .container::after {
            content: '🍷';
            position: absolute;
            font-size: 100px;
            opacity: 0.05;
            left: -15px;
            bottom: -15px;
            transform: rotate(15deg);
        }

        h1 {
            text-align: center;
            color: #8b4513;
            font-size: 2.8em;
            margin-bottom: 10px;
            text-shadow: 3px 3px 6px rgba(212, 130, 47, 0.3);
            position: relative;
        }

        .subtitle {
            text-align: center;
            color: #d4822f;
            font-size: 1.3em;
            margin-bottom: 35px;
            font-style: italic;
        }

        .score {
            text-align: center;
            font-size: 1.4em;
            margin-bottom: 25px;
            color: #8b4513;
            font-weight: bold;
            background: linear-gradient(90deg, #ff8c42, #d4822f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .question-container {
            background: linear-gradient(135deg, #fff9e6 0%, #ffefd5 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
            border-left: 6px solid #d4822f;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.15);
            position: relative;
        }

        .question {
            font-size: 1.4em;
            color: #654321;
            margin-bottom: 25px;
            font-weight: 600;
            line-height: 1.5;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            padding: 16px 24px;
            font-size: 1.1em;
            border: 3px solid #d4822f;
            background: linear-gradient(135deg, #fff 0%, #ffefd5 100%);
            color: #654321;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(139, 69, 19, 0.1);
            position: relative;
        }

        button:hover {
            background: linear-gradient(135deg, #ffd89b 0%, #ff8c42 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.25);
            color: #fff;
            border-color: #ff8c42;
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .correct {
            background: linear-gradient(135deg, #90EE90 0%, #3CB371 100%) !important;
            border-color: #228B22 !important;
            color: white !important;
            font-weight: bold;
        }

        .incorrect {
            background: linear-gradient(135deg, #ffcccc 0%, #ff6b6b 100%) !important;
            border-color: #cc0000 !important;
            color: white !important;
        }

        .challenge {
            background: linear-gradient(135deg, #ffe4b3 0%, #ffd89b 100%);
            border: 3px dashed #d4822f;
            border-radius: 20px;
            padding: 25px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #8b4513;
            font-weight: 600;
            text-align: center;
            animation: shake 0.5s;
            box-shadow: 0 6px 15px rgba(212, 130, 47, 0.3);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .next-btn {
            width: 100%;
            margin-top: 25px;
            background: linear-gradient(135deg, #d4822f 0%, #ff8c42 100%);
            color: white;
            font-size: 1.3em;
            padding: 18px;
            border: none;
            font-weight: bold;
            box-shadow: 0 6px 20px rgba(212, 130, 47, 0.4);
        }

        .next-btn:hover {
            background: linear-gradient(135deg, #ff8c42 0%, #d4822f 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(212, 130, 47, 0.5);
        }

        .result {
            text-align: center;
            padding: 30px;
        }

        .result h2 {
            font-size: 2.5em;
            color: #8b4513;
            margin-bottom: 20px;
        }

        .result p {
            font-size: 1.4em;
            color: #654321;
            margin-bottom: 30px;
        }

        .emoji {
            font-size: 4em;
            margin-bottom: 20px;
            display: block;
        }

        .restart-btn {
            background: linear-gradient(135deg, #ff8c42 0%, #d4822f 100%);
            color: white;
            padding: 18px 40px;
            font-size: 1.3em;
            border: none;
            font-weight: bold;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍕 Quizchlanie 🍷</h1>
        <p class="subtitle">Sprawdź swoją wiedzę!</p>
        
        <div id="quiz">
            <div class="score">Pytanie: <span id="current">1</span> / <span id="total">5</span></div>
            <div class="score">Punkty: <span id="score">0</span></div>
            
            <div class="question-container">
                <div class="question" id="question"></div>
                <div id="challenge" class="hidden challenge"></div>
                <div class="options" id="options"></div>
            </div>
            
            <button class="next-btn hidden" id="nextBtn" onclick="nextQuestion()">Następne pytanie →</button>
        </div>

        <div id="result" class="result hidden">
            <span class="emoji" id="resultEmoji"></span>
            <h2 id="resultTitle"></h2>
            <p id="resultText"></p>
            <button class="restart-btn" onclick="restartQuiz()">🔄 Zagraj ponownie</button>
        </div>
    </div>

    <script>
        const challengePool = [
            "Dziewczyny pija jak zle odpowiedzialy(ci co dobrze nie)",
            "Chlopcy pija jak zle odpowiedzieli(ci co dobrze nie)",
            "Pijesz z prowadzacym",
            "Pijesz shota lub lyka piwa",
            "Wodospad, 3 duze lyki",
            "Pijesz brudzia z wybrana osoba",
            "Rzucasz moneta i jak wypadnie Orzel to pijesz",
            "Wszyscy pija",
            "Wszyscy pija oprocz prowadzacego",
            "Jesli odpowiesz zle na nastepnie pytanie to pijesz wodospad",
            "Napij sie soczku, nie jestem az taki zlosliwy"
        ];

        let quizData = [
          {
              question: "Jak nazywa się aktor grający Jana Pawła w serialu 1670?",
              options: ["Michał Sikorski", "Bartłomiej Topa", "Borys Szyc", "Tadeusz Drozda"],
              correct: 1,
              challenge: "🎬 Zrób minę jak w scenie dramatycznego objawienia!"
          },
          {
              question: "Stolica Stanów Zjednoczonych?",
              options: ["Nowy York", "Waszyngton", "Ottawa", "Boston"],
              correct: 1,
              challenge: "🗽 Powiedz coś z akcentem amerykańskim!"
          },
          {
              question: "Która z poniższych rzek nie płynie przez terytorium Francji?",
              options: ["Loara", "Ren", "Sekwana", "Dunaj"],
              correct: 3,
              challenge: "🌊 Udawaj falę oceanu przez 10 sekund!"
          },
          {
              question: "Ile to 1/8 + 1/3?",
              options: ["1/11", "2/11", "11/24", "24/11"],
              correct: 2,
              challenge: "🧮 Zrób mądrą minę i powiedz: 'Matematyka to sztuka!'"
          },
          {
              question: "Kto wygrał 7. edycję programu Love Island?",
              options: ["Armin i Laura", "Agata i Hubert", "Donald i Trump", "Jarek i Zuza"],
              correct: 1,
              challenge: "❤️ Powiedz najbardziej romantyczne zdanie, jakie przyjdzie Ci do głowy!"
          },
          {
              question: "Co to jest? Piecze, pierze i się kręci?",
              options: ["Twoja stara", "Koło Gospodyń Wiejskich", "Pralka z funkcją pieczenia", "Robot kuchenny Halina 3000"],
              correct: 1,
              challenge: "😂 Opowiedz najlepszy żart, jaki znasz!"
          },
          {
              question: "Który z polityków jest potocznie nazywany przez swoją żonę 'Tygrysek'?",
              options: ["Donald Tusk", "Roman Giertych", "Władysław Kosiniak-Kamysz", "Karol Krawczyk"],
              correct: 2,
              challenge: "🐯 Zarycz jak prawdziwy tygrys!"
          },
          {
              question: "Ile trzeba wypić piw Perła 6% (0,5l), by dostarczyć tyle alkoholu co z 0,5l wódki 40%?",
              options: ["6", "6,66", "7", "Nie wiem, ale spróbujmy!"],
              correct: 1,
              challenge: "🍺 Opowiedz o swoim najdziwniejszym doświadczeniu imprezowym!"
          },
          {
              question: "Co po polsku znaczy angielskie słowo 'sympathetic'?",
              options: ["Sympatyczny", "Życzliwy", "Współczujący", "Zgodny"],
              correct: 2,
              challenge: "🇬🇧 Powiedz trzy słowa po angielsku, które brzmią śmiesznie!"
          },
          {
              question: "Która z tych drużyn piłkarskich nie jest polskim klubem?",
              options: ["Jagiellonia Białystok", "Banik Ostrawa", "Warta Poznań", "Piast Gliwice"],
              correct: 1,
              challenge: "⚽ Udawaj komentatora sportowego przez 10 sekund!"
          },
          {
              question: "Jeśli pięć kotów łapie pięć myszy w pięć minut, ile czasu zajmie dziesięciu kotom złapanie dziesięciu myszy?",
              options: ["5 minut", "10 minut", "15 minut", "20 minut"],
              correct: 0,
              challenge: "🐱 Zamiaucz trzy razy jak prawdziwy kot!"
          },
          {
              question: "W którym z tych filmów nie grał Leonardo DiCaprio?",
              options: ["Titanic", "Incepcja", "Gone Girl", "Catch Me If You Can"],
              correct: 2,
              challenge: "🎬 Odegraj dramatyczną scenę z Titanica!"
          },
          {
              question: "Kto napisał książkę 'Mały Książę'?",
              options: ["Christian Andersen", "Antoine de Saint-Exupéry", "Nieważne, piję z prowadzącym", "Adam Mickiewicz"],
              correct: 1,
              challenge: "📖 Powiedz swoją ulubioną życiową mądrość!"
          },
          {
              question: "Im więcej suszę, tym bardziej jestem mokry. Co to jest?",
              options: ["Gąbka", "Ręcznik", "Lód", "Chusteczka"],
              correct: 1,
              challenge: "💦 Opowiedz coś, co zawsze Cię rozśmiesza!"
          },
          {
              question: "W jaką grę sportową grano na Księżycu?",
              options: ["Koszykówka", "Baseball", "Golf", "Szachy"],
              correct: 2,
              challenge: "🏌️ Udawaj, że uderzasz kijem golfowym!"
          },
          {
              question: "Jaka jest najczęstsza przyczyna rozpadu związków małżeńskich?",
              options: ["Różnice w podejściu do posiadania dzieci", "Niezgodność charakterów", "Zdrada", "Nadmierne spożywanie alkoholu"],
              correct: 1,
              challenge: "💔 Opowiedz o najbardziej absurdalnej kłótni, jaką słyszałeś!"
          },
          {
              question: "Jak powstaje czarna dziura?",
              options: [
                  "Kiedy gwiazda zużyje cały wodór i eksploduje, a jej jądro zapada się pod własną grawitacją",
                  "Kiedy planeta staje się zbyt gorąca i zapada się w sobie",
                  "Kiedy cząsteczki ciemnej materii łączą się w jeden punkt",
                  "Kiedy Słońce wchodzi w fazę czerwonego olbrzyma i przemienia się w czarną dziurę"
              ],
              correct: 0,
              challenge: "🌌 Powiedz coś jak kosmiczny filozof!"
          },
          {
              question: "Do kogo porównał rozum swojego syna jeżdżącego na crossie po polu facet z filmiku 'Eee, nie po sionym'?",
              options: ["Babki", "Matki", "Anki", "Donalda Tuska"],
              correct: 1,
              challenge: "😂 Zacytuj mema, który zawsze Cię rozwala!"
          },
          {
              question: "Jaki jest wiek uprawniający do kupna alkoholu w USA?",
              options: ["18", "21", "20", "14 za zgodą rodzica"],
              correct: 1,
              challenge: "🍻 Opowiedz o najdziwniejszym napoju, jakiego próbowałeś!"
          },
          {
              question: "Jaka jest wysokość szczytu Mount Everest?",
              options: ["8820", "8848", "8900", "9000"],
              correct: 1,
              challenge: "⛰️ Udawaj, że wspinasz się po górze przez 10 sekund!"
          },
           {
              question: "Który z tych artystów nie wystąpił nigdy na Stadionie Narodowym?",
              options: ["Taco Hemingway", "Sanah", "Bedoes", "Max Korzh"],
              correct: 2,
              challenge: "🎤 Zaśpiewaj fragment dowolnej piosenki Bedoesa!"
          },
          {
              question: "W której z wymienionych grup wyrazów WSZYSTKIE są napisane poprawnie?",
              options: [
                  "Grupa 1: rzerzucha, przepiórka, wechikuł",
                  "Grupa 2: chochlik, rzodkiewka, orenżada",
                  "Grupa 3: bagaż, skuwka, gżegżółka",
                  "Grupa 4: alkocholik, dobze, tomarz"
              ],
              correct: 2,
              challenge: "📚 Wymów słowo 'gżegżółka' trzy razy szybko!"
          },
          {
              question: "Zgadnij liczbę od 1 do 4, o której pomyślał prowadzący!",
              options: ["1", "2", "3", "4"],
              correct: 2,
              challenge: "🎲 Rzuć monetą — orzeł to prawda, reszka to wyzwanie!"
          },
          {
              question: "Która z tych rzeczy NIE nadaje się na przynętę do łowienia ryb?",
              options: ["Robak", "Kukurydza", "Kiełbasa", "Chleb"],
              correct: 2,
              challenge: "🐟 Udawaj rybę przez 10 sekund!"
          },
          {
              question: "Dokończ tekst piosenki Cypisa – 'Tylko jedno w głowie mam': 'Gorączka w kurwę się nasila, poharatany jak dupa fakira, jak zdzira wymiętolony...'",
              options: ["Siedzę sobie tu spocony", "Leżę kurwa rozpalony", "Zjadłem sobie korniszony", "Porno mode już odpalony"],
              correct: 1,
              challenge: "🎶 Zarepkuj coś od Cypisa!"
          },
          {
              question: "Które z tych miast nie leży w województwie lubelskim?",
              options: ["Biała Podlaska", "Łuków", "Leżajsk", "Biłgoraj"],
              correct: 2,
              challenge: "🗺️ Wymień 3 miasta z województwa lubelskiego!"
          },
          {
              question: "Jak nazywa się najmniejsze państwo na świecie pod względem powierzchni?",
              options: ["Monako", "San Marino", "Watykan", "Liechtenstein"],
              correct: 2,
              challenge: "⛪ Zrób gest błogosławieństwa jak papież!"
          },
          {
              question: "Jakie miasto było stolicą Polski przed Warszawą?",
              options: ["Gdańsk", "Kraków", "Poznań", "Wrocław"],
              correct: 1,
              challenge: "🏰 Powiedz 'smok wawelski' z groźną miną!"
          },
          {
              question: "Jaka jest maksymalna prędkość dla ciężarówek w terenie zabudowanym przy ograniczeniu do 70 km/h?",
              options: ["50 km/h", "60 km/h", "55 km/h", "70 km/h"],
              correct: 0,
              challenge: "🚛 Zrób dźwięk klaksonu ciężarówki!"
          },
          {
              question: "W jakiej z tych sytuacji nie można wyprzedzać?",
              options: [
                  "Na przejściu z sygnalizacją świetlną, gdy mamy zielone światło",
                  "Na skrzyżowaniu",
                  "W strefie zamieszkania",
                  "Gdy wyprzedzamy samochód marki BMW"
              ],
              correct: 1,
              challenge: "🚦 Udawaj, że prowadzisz samochód i zatrzymujesz się na światłach!"
          },
          {
              question: "Jaka jest druga zasada dynamiki Newtona?",
              options: [
                  "Przyspieszenie ciała jest wprost proporcjonalne do działającej na nie siły i odwrotnie proporcjonalne do jego masy.",
                  "Jeśli na ciało nie działa żadna siła lub działające siły się równoważą, ciało pozostaje w spoczynku lub porusza się ruchem jednostajnym prostoliniowym.",
                  "Każdej sile działającej na ciało towarzyszy siła równa co do wartości i przeciwnie skierowana działająca na drugie ciało.",
                  "Piję z prowadzącym."
              ],
              correct: 0,
              challenge: "🧠 Powiedz coś, co brzmi mądrze, ale nie ma sensu!"
          },
          {
              question: "Jak powiemy o czymś, co obeszliśmy? Powiemy, że jezioro zostało przez nas...",
              options: ["obszedłe", "obejśnięte", "obeszłe", "oblazłe"],
              correct: 2,
              challenge: "🗣️ Powiedz to zdanie w stylu profesora z Uniwersytetu!"
          },
          {
              question: "Panczeniści do uprawiania swojego sportu potrzebują...",
              options: ["Karabinku", "Łyżew", "Nart", "Sanek"],
              correct: 1,
              challenge: "⛸️ Udawaj, że ślizgasz się po lodzie!"
          },
          {
              question: "Temudżyn to znany później...",
              options: ["Marco Polo", "Czyngis-Chan", "Sulejman Wspaniały", "Michał Wiśniewski"],
              correct: 1,
              challenge: "🏇 Krzyknij wojowniczo jak Mongoł z XIII wieku!"
          },
          {
              question: "Na tablicy Mendelejewa symbolem P oznaczony jest...?",
              options: ["Potas", "Azot", "Fosfor", "H2O"],
              correct: 2,
              challenge: "🧪 Udawaj naukowca, który właśnie dokonał odkrycia!"
          },
          {
              question: "Rozwiąż zadanie: -7 - (5 - 24) = ... ?",
              options: ["-36", "12", "-26", "-10"],
              correct: 2,
              challenge: "🧮 Policz coś w pamięci i udawaj, że jesteś geniuszem matematyki!"
          },
          {
              question: "Ile trwa dekada?",
              options: ["5 lat", "10 lat", "100 lat", "1000 lat"],
              correct: 1,
              challenge: "📆 Powiedz, ile masz lat, ale w stylu teleturnieju!"
          },
          {
              question: "Który z tych roków nie należy do XIX wieku?",
              options: ["1999", "2000", "1900", "1901"],
              correct: 1,
              challenge: "⌛ Cofnij się w czasie i zrób minę, jakbyś był w XIX wieku!"
          },
          {
              question: "Czym jest enklawa?",
              options: [
                  "Część terytorium państwa całkowicie otoczona przez inne państwo",
                  "Małe państwo na wyspie",
                  "Obszar o specjalnym statusie gospodarczym",
                  "Rodzaj jeziora"
              ],
              correct: 0,
              challenge: "🗺️ Wymień 3 państwa, które znasz bez googlowania!"
          },
          {
              question: "Którą z tych ryb możemy spotkać w wodach polskich (Zoo się nie liczy)?",
              options: ["Rekin", "Pirania", "Sandacz", "Fileta"],
              correct: 2,
              challenge: "🐠 Zrób minę jak ryba, która właśnie złapała haczyk!"
          },
          {
              question: "What is the answer for this question (Jaka jest odpowiedź na to pytanie)?",
              options: ["this question", "answer", "what", "the answer"],
              correct: 2,
              challenge: "🤔 Powiedz 'what' z najlepszym brytyjskim akcentem!"
          },
          {
              question: "Ile nóg ma pies?",
              options: ["2 nogi + 2 łapy", "3", "pierwiastek z 16", "4 silnia (4!)"],
              correct: 2,
              challenge: "🐶 Zaszczekaj trzy razy jak prawdziwy pies!"
          },
          {
              question: "W co wierzą scjentolodzy?",
              options: [
                  "W to, że człowiek jest nieśmiertelną istotą duchową zwaną thetanem",
                  "W potęgę nauki i eksperymentów laboratoryjnych",
                  "W reinkarnację zwierząt i moc kryształów",
                  "W to, że kosmici kontrolują pogodę i internet"
              ],
              correct: 0,
              challenge: "👽 Powiedz: 'Jestem thetanem!' w tonie jak z filmu sci-fi!"
          },
          {
              question: "Dokończ: Michał Jakubowski...",
              options: [
                  "Robi herbatę z wody po pierogach",
                  "Powinien słuchać się zawsze swojej dziewczyny",
                  "Michał to ziomal",
                  "Wkurza mnie ten gościu"
              ],
              correct: 2,
              forceChallenge: "Wodospad x2 6 lykow",
              challenge: "☕ Powiedz coś, co brzmiałoby jak motto Michała Jakubowskiego!"
          },
          {
              question: "Jak brzmi Prawo Pascala (odpowiedzi potoczne są dopuszczalne)?",
              options: [
                  "Kto wybił, ten zap***la",
                  "Ciecz zawsze płynie z miejsca o niższym ciśnieniu do miejsca o wyższym",
                  "Kto skoczył, ten zap***la",
                  "Ciśnienie w cieczy działa tylko w kierunku pionowym"
              ],
              correct: 0,
              challenge: "💧 Powiedz 'Prawo Pascala!' z powagą jak profesor fizyki!"
          },
          {
              question: "Czy astrologia ma podstawy naukowe i jest uznawana za naukę?",
              options: [
                  "Tak",
                  "Nie, to nie ma podstaw naukowych, poza tym to bez sensu — układ gwiazd nie może wpływać na to, czy osiągnę sukces czy nie",
                  "Tak, gwiazdy powiedziały mi przyszłość",
                  "Odpierdol się od zodiakar, okej?"
              ],
              correct: 1,
              challenge: "🔮 Powiedz swój znak zodiaku z pełną powagą!"
          },
          {
              question: "Kto zastał Polskę drewnianą, a zostawił murowaną?",
              options: [
                  "Kazimierz III Wielki",
                  "Bolesław Chrobry",
                  "Nikt, wtf co to za pytanie",
                  "Donald Giertych"
              ],
              correct: 2,
              challenge: "🏰 Zrób gest budowania muru niczym Kazimierz Wielki!"
          },
          {
              question: "Która z dzielnic Warszawy protestowała przeciwko powstaniu Dino?",
              options: ["Mokotów", "Wilanów", "Praga", "Żoliborz"],
              correct: 1,
              challenge: "🦖 Zarycz jak dinozaur z Wilanowa!"
          },
          {
              question: "Co jest po pytaniu w tym pytaniu?",
              options: ["Nic", "?", "Jest", "w"],
              correct: 3,
              challenge: "🌀 Powiedz to zdanie od tyłu, jeśli potrafisz!"
          },
          {
              question: "Pod jaki numer proponował dzwonić nagrywający film pod tytułem 'Paweł Jumper'?",
              options: ["0900", "700", "0700", "0600"],
              correct: 2,
              challenge: "📞 Powiedz 'Halo, tu Paweł Jumper!' z pełnym zaangażowaniem!"
          }
      ];
      
        
        quizData = shuffleArray(quizData);

        function getRandomChallenge() {
            const randomIndex = Math.floor(Math.random() * challengePool.length);
            return challengePool[randomIndex];
        }

        let currentQuestion = 0;
        let score = 0;
        let answered = false;
        let shuffledOptions = [];

        function shuffleArray(array) {
            const shuffled = [...array];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            return shuffled;
        }

        function loadQuestion() {
            answered = false;
            const q = quizData[currentQuestion];
            document.getElementById('question').textContent = q.question;
            document.getElementById('current').textContent = currentQuestion + 1;
            document.getElementById('total').textContent = quizData.length;
            
            const optionsDiv = document.getElementById('options');
            optionsDiv.innerHTML = '';
            
            // Tworzymy tablicę z odpowiedziami i informacją czy to poprawna odpowiedź
            const optionsWithCorrect = q.options.map((option, index) => ({
                text: option,
                isCorrect: index === q.correct
            }));
            
            // Losujemy kolejność
            shuffledOptions = shuffleArray(optionsWithCorrect);
            
            // Tworzymy przyciski
            shuffledOptions.forEach((option, index) => {
                const btn = document.createElement('button');
                btn.textContent = option.text;
                btn.onclick = () => checkAnswer(index);
                optionsDiv.appendChild(btn);
            });
            
            document.getElementById('challenge').classList.add('hidden');
            document.getElementById('nextBtn').classList.add('hidden');
        }

        function checkAnswer(selected) {
            const buttons = document.querySelectorAll('.options button');
            
            if (shuffledOptions[selected].isCorrect) {
                if (!answered) {
                    answered = true;
                    score++;
                    document.getElementById('score').textContent = score;
                }
                
                buttons.forEach((btn, index) => {
                    btn.disabled = true;
                    if (shuffledOptions[index].isCorrect) {
                        btn.classList.add('correct');
                    }
                });
                
                document.getElementById('nextBtn').classList.remove('hidden');
            } else {
                buttons[selected].classList.add('incorrect');
                buttons[selected].disabled = true;
            }
            
            const challengeDiv = document.getElementById('challenge');
            
            if (challengeDiv && challengeDiv.classList.contains('hidden')) {
              if (quizData[currentQuestion]?.forceChallenge) {
                    const challengeDiv = document.getElementById('challenge');
                    challengeDiv.textContent = 'Wyzwanie dla tych co zle odpowiedzieli: ' + quizData[currentQuestion].forceChallenge 
                    challengeDiv.classList.remove('hidden');
                } else if (Math.random() < 0.95) {
                    const challengeDiv = document.getElementById('challenge');
                    challengeDiv.textContent = 'Wyzwanie dla tych co zle odpowiedzieli: ' + getRandomChallenge();
                    challengeDiv.classList.remove('hidden');
                } else {
                    const challengeDiv = document.getElementById('challenge');
                    challengeDiv.textContent = 'Tym razem nie ma wyzwania...!';
                    challengeDiv.classList.remove('hidden');
                }
            }
        }

        function nextQuestion() {
            currentQuestion++;
            if (currentQuestion < quizData.length) {
                loadQuestion();
            } else {
                showResult();
            }
        }

        function showResult() {
            document.getElementById('quiz').classList.add('hidden');
            document.getElementById('result').classList.remove('hidden');
            
            const percentage = (score / quizData.length) * 100;
            let emoji, title, text;
            
            if (percentage === 100) {
                emoji = '🏆';
                title = 'Perfekcyjnie!';
                text = `Wow! ${score}/${quizData.length} punktów! Jesteś mistrzem quizów!`;
            } else if (percentage >= 60) {
                emoji = '🎉';
                title = 'Świetnie!';
                text = `Brawo! ${score}/${quizData.length} punktów! Całkiem nieźle ci poszło!`;
            } else {
                emoji = '😅';
                title = 'Można lepiej!';
                text = `${score}/${quizData.length} punktów. Ale za to miałeś/aś fajne wyzwania!`;
            }
            
            document.getElementById('resultEmoji').textContent = emoji;
            document.getElementById('resultTitle').textContent = title;
            document.getElementById('resultText').textContent = text;
        }

        function restartQuiz() {
            currentQuestion = 0;
            score = 0;
            document.getElementById('score').textContent = score;
            document.getElementById('quiz').classList.remove('hidden');
            document.getElementById('result').classList.add('hidden');
            loadQuestion();
        }

        loadQuestion();
    </script>
</body>
</html>
