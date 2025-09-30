<?php
namespace sokol\ColdHot;

class Game {
    private string $secret;
    private int $attempts = 0;
    private bool $isOver = false;

    public function __construct() {
        $this->secret = $this->generate();
    }

    private function generate(): string {
        $digits = range(0, 9);
        shuffle($digits);
        if ($digits[0] === 0) {
            foreach ($digits as $i => $d) {
                if ($d !== 0) {
                    $digits[0] = $d;
                    $digits[$i] = 0;
                    break;
                }
            }
        }
        return implode('', array_slice($digits, 0, 3));
    }

    public function play(): void {
        View::showMessage("Компьютер загадал число. Введите 'exit' для выхода.");
        while (!$this->isOver) {
            $guess = View::prompt("Попытка #".($this->attempts+1)."> ");
            if (strtolower($guess) === 'exit') {
                View::showMessage("Вы вышли. Загаданное число: ".$this->secret);
                return;
            }
            $this->attempts++;
            $hints = $this->checkGuess($guess);
            View::showMessage("Подсказки: ".implode(' ', $hints));
            if ($guess === $this->secret) {
                $this->isOver = true;
                View::showMessage("Угадали! Число {$this->secret}, попыток: {$this->attempts}");
            }
        }
    }

    private function checkGuess(string $guess): array {
        if (!preg_match('/^\d{3}$/', $guess)) {
            return ['Ошибка ввода'];
        }
        $hints = [];
        for ($i=0; $i<3; $i++) {
            if ($guess[$i] === $this->secret[$i]) {
                $hints[] = 'Горячо';
            } elseif (strpos($this->secret, $guess[$i]) !== false) {
                $hints[] = 'Тепло';
            }
        }
        if (!$hints) $hints[] = 'Холодно';
        sort($hints);
        return $hints;
    }
}
