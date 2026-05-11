// Поддержка разных реализаций SpeechRecognition в браузерах
const SpeechRecognition =
  window.SpeechRecognition || window.webkitSpeechRecognition;

const statusEl = document.getElementById("status");
const logEl = document.getElementById("log");
const startBtn = document.getElementById("startBtn");
const stopBtn = document.getElementById("stopBtn");

if (!SpeechRecognition) {
  statusEl.textContent =
    "Ваш браузер не поддерживает Web Speech API (SpeechRecognition). Попробуйте Chrome/Edge.";
} else {
  const recognition = new SpeechRecognition();

  // Настройки распознавания
  recognition.lang = "ru-RU";          // Русский язык
  recognition.continuous = true;       // Слушаем постоянно
  recognition.interimResults = false;  // Нас интересуют только финальные результаты

  recognition.onstart = () => {
    statusEl.textContent = "Слушаю… Говорите команду!";
  };

  recognition.onend = () => {
    statusEl.textContent = "Микрофон выключен";
  };

  recognition.onerror = (event) => {
    addLog("Ошибка распознавания: " + event.error);
  };

  recognition.onresult = (event) => {
    // Берём последний результат
    const lastResult = event.results[event.results.length - 1];
    if (!lastResult.isFinal) return;

    const text = lastResult[0].transcript.toLowerCase().trim();
    addLog("Распознано: " + text);

    handleCommand(text);
  };

  startBtn.addEventListener("click", () => {
    try {
      recognition.start();
    } catch (e) {
      // Chrome может кидать ошибку "already started" — игнорируем
      console.warn(e);
    }
  });

  stopBtn.addEventListener("click", () => {
    recognition.stop();
  });
}

/**
 * Обработка текста команды
 */
function handleCommand(text) {
  // Реагируем только на фразы, начинающиеся со слова "дом"
  if (!text.startsWith("дом")) {
    addLog("Игнор: нет ключевого слова «дом»");
    return;
  }

  const command = text.replace(/^дом\s*/, ""); // убрать "дом" в начале

  if (command.includes("включи свет")) {
    addLog("Команда: ВКЛЮЧИТЬ СВЕТ");
    sendCommandToServer("LIGHT_ON");
  } else if (command.includes("выключи свет")) {
    addLog("Команда: ВЫКЛЮЧИТЬ СВЕТ");
    sendCommandToServer("LIGHT_OFF");
  } else if (command.includes("включи телевизор") || command.includes("включи тв")) {
    addLog("Команда: ВКЛЮЧИТЬ ТЕЛЕВИЗОР");
    sendCommandToServer("TV_ON");
  } else if (command.includes("выключи телевизор") || command.includes("выключи тв")) {
    addLog("Команда: ВЫКЛЮЧИТЬ ТЕЛЕВИЗОР");
    sendCommandToServer("TV_OFF");
  } else if (command.includes("включи чайник") || command.includes("вскипяти чайник")) {
    addLog("Команда: ВКЛЮЧИТЬ ЧАЙНИК");
    sendCommandToServer("KETTLE_ON");
  } else {
    addLog("Команда не распознана");
  }
}

/**
 * Отправка команды на твой backend умного дома
 * Здесь ты подставишь реальный URL и логику (HTTP, WebSocket, MQTT и т.п.)
 */
function sendCommandToServer(action) {
  // Пример через HTTP POST на локальный сервер
  fetch("http://192.168.0.100:3000/api/command", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ action }),
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error("HTTP " + res.status);
      }
      return res.json().catch(() => ({}));
    })
    .then((data) => {
      addLog("Сервер принял команду: " + action);
    })
    .catch((err) => {
      addLog("Ошибка отправки команды на сервер: " + err.message);
    });
}

/**
 * Лог внизу страницы
 */
function addLog(message) {
  const time = new Date().toLocaleTimeString();
  logEl.textContent += `[${time}] ${message}\n`;
  logEl.scrollTop = logEl.scrollHeight;
}
