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
        if (!$this->sessionStarted()) {
            return null;
        }

        $key = array_shift($index);
        if ($this->has($key)) {
            $data = $_SESSION[$key];
            if (!empty($index)) {
                foreach ($index as $arg) {
                    if (isset($data[$arg])) {
                        $data = $data[$arg];
                    } else {
                        return null;
                    }
                }
            }
            return $data;
        }

        return null;
    }

    /**
     * @param string ...$index List of index : one per argument.
     * @return bool
     */
    public function has(string ...$index): bool
    {
        if (!$this->sessionStarted()) {
            return false;
        }

        $key = array_shift($index);
        if (!isset($_SESSION[$key])) {
            return false;
        }

        $data = $_SESSION[$key];
        if (!empty($index)) {
            foreach ($index as $arg) {
                if (isset($data[$arg])) {
                    $data = $data[$arg];
                } else {
                    return false;
                }
            }
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
     * @param $value
     * @param string ...$index
     */
    public function add(string $key, $value, string ...$index): void
    {
        if (empty($index)) {
            throw new RuntimeException('Class Session, function add. The index parameter (third parameter) is required.');
        }
        $firstIndex = array_shift($index);
        $data = $this->get($firstIndex);
        if (empty($index)) {
            //Numeric array
            if (isset($data[$key]) && is_array($data[$key]) && array_key_exists(0, $data[$key])) {
                $data[$key][] = $value;
                $this->set($firstIndex, $data);
                return;
            }
            //Associative array
            $data[$key] = $value;
            $this->set($firstIndex, $data);
            return;
        }
        $this->set($firstIndex, ArrayManipulation::addKeyValue(...array_merge([$data], [$key], [$value], $index)));
    }

    /**
     * @param string ...$index
     */
    public function delete(string ...$index): void
    {
        if (!$this->sessionStarted()) {
            return;
        }

        if (count($index) > 1) {
            $sessionIndex = array_shift($index);

            if ($this->has($sessionIndex)) {
                $data = $this->get($sessionIndex);
                $newData = ArrayManipulation::removeKey(...array_merge([$data], $index));
                $this->set($sessionIndex, $newData);
            }

            return;
        }

        if ($this->has($index[0])) {
            unset($_SESSION[$index[0]]);
        }
    }

    /**
     * @return bool true if the $_SESSION is started.
     */
    private function sessionStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

}