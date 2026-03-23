<?php

namespace MulerTech\HttpRequest\Session;

use MulerTech\ArrayManipulation\ArrayManipulation;

/**
 * Class Session.
 *
 * @author Sébastien Muler
 */
class Session
{
    /**
     * @param string ...$index List of index : one per argument.
     *
     * @return mixed|null
     */
    public function get(string ...$index): mixed
    {
        if (empty($index) || !$this->sessionStarted()) {
            return null;
        }

        $key = array_shift($index);
        if (!$this->has($key)) {
            return null;
        }

        $data = $_SESSION[$key];
        foreach ($index as $arg) {
            if (is_array($data) && isset($data[$arg])) {
                $data = $data[$arg];
                continue;
            }

            return null;
        }

        return $data;
    }

    /**
     * @param string ...$index List of index : one per argument.
     */
    public function has(string ...$index): bool
    {
        if (empty($index) || !$this->sessionStarted()) {
            return false;
        }

        $key = array_shift($index);
        if (!isset($_SESSION[$key])) {
            return false;
        }

        $data = $_SESSION[$key];
        foreach ($index as $arg) {
            if (is_array($data) && isset($data[$arg])) {
                $data = $data[$arg];
                continue;
            }

            return false;
        }

        return true;
    }

    public function set(string $key, mixed $value): void
    {
        if (!$this->sessionStarted()) {
            session_start();
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Add a key and its value onto a $_SESSION[$index][otherindex][andotherindex]...[$key] = $value.
     */
    public function add(string $key, mixed $value, string ...$index): void
    {
        if (empty($index)) {
            throw new \RuntimeException('Class Session, function add. The index parameter (third parameter) is required.');
        }

        $firstIndex = array_shift($index);
        $data = $this->get($firstIndex);

        if (is_string($data)) {
            $this->set($firstIndex, [$key => $value]);

            return;
        }

        if (!is_array($data)) {
            $data = [];
        }

        $this->set($firstIndex, ArrayManipulation::addKeyValue($data, $key, $value, ...$index));
    }

    public function delete(string ...$index): void
    {
        if (empty($index) || !$this->sessionStarted()) {
            return;
        }

        $sessionIndex = array_shift($index);
        if (empty($index)) {
            unset($_SESSION[$sessionIndex]);

            return;
        }

        if ($this->has($sessionIndex)) {
            $data = $this->get($sessionIndex);
            if (is_array($data)) {
                $newData = ArrayManipulation::removeKey($data, ...$index);
                $this->set($sessionIndex, $newData);
            }
        }
    }

    private function sessionStarted(): bool
    {
        return PHP_SESSION_ACTIVE === session_status();
    }
}
