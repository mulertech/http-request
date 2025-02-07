<?php

namespace MulerTech\HttpRequest\Session;

use MulerTech\ArrayManipulation\ArrayManipulation;
use RuntimeException;

/**
 * Class Session
 * @package MulerTech\HttpRequest\Session
 * @author Sébastien Muler
 */
class Session
{
    /**
     * @param string ...$index List of index : one per argument.
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
            if (isset($data[$arg])) {
                $data = $data[$arg];
                continue;
            }

            return null;
        }

        return $data;
    }

    /**
     * @param string ...$index List of index : one per argument.
     * @return bool
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
            if (isset($data[$arg])) {
                $data = $data[$arg];
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void
    {
        if (!$this->sessionStarted()) {
            session_start();
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Add a key and its value onto a $_SESSION[$index][otherindex][andotherindex]...[$key] = $value
     * @param string $key
     * @param mixed $value
     * @param string ...$index
     */
    public function add(string $key, mixed $value, string ...$index): void
    {
        if (empty($index)) {
            throw new RuntimeException(
                'Class Session, function add. The index parameter (third parameter) is required.'
            );
        }

        $firstIndex = array_shift($index);
        $data = $this->get($firstIndex);

        $this->set($firstIndex, ArrayManipulation::addKeyValue($data, $key, $value, ...$index));
    }

    /**
     * @param string ...$index
     */
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
            $newData = ArrayManipulation::removeKey($data, ...$index);
            $this->set($sessionIndex, $newData);
        }
    }

    /**
     * @return bool
     */
    private function sessionStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

}
