<?php
namespace TurboPancake;

use DateTime;
use TurboPancake\Validator\ValidationError;
use mysql_xdevapi\Exception;

class Validator {

    /**
     * @var array
     */
    private $fields;

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * @var string[]
     */
    private $customNames = [];

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Permet de rendre requis des champs
     * @param string ...$fields
     * @return self
     */
    public function required(string ...$fields): self
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $this->fields)) {
                $this->addError($field, 'required');
            }
        }
        return $this;
    }

    /**
     * Verifie que les champs sont presents et non-vides
     * @param string ...$fields
     * @return self
     */
    public function filled(string ...$fields): self
    {
        foreach ($fields as $field) {
            $value = $this->getValue($field);
            if (is_null($value) OR empty(trim($value))) {
                $this->addError($field, 'empty');
            }
        }
        return $this;
    }

    /**
     * @param string $field
     * @param int|null $min
     * @param int|null $max
     * @return Validator
     * @throws \Exception
     */
    public function length(string $field, ?int $min, ?int $max = null): self
    {
        if (is_null($min) AND is_null($max)) {
            throw new \Exception(
                'Validation error: you need to define a least one length parameter in addition to the field name.',
                E_ERROR
            );
        }
        if (!is_null($min) AND !is_null($max) AND $min > $max) {
            throw new \Exception(
                'Validation error: the max length must be least the value of the min length.',
                E_ERROR
            );
        }

        $value = $this->getValue($field);
        $length = mb_strlen($value);
        if (
            !is_null($min) AND
            !is_null($max) AND
            ($length < $min OR $length > $max)
        ) {
            $this->addError($field, 'between', [$min, $max]);
            return $this;
        }
        if (
            !is_null($min) AND
            $length < $min
        ) {
            $this->addError($field, 'short', [$min]);
            return $this;
        }
        if (
            !is_null($max) AND
            $length > $max
        ) {
            $this->addError($field, 'long', [$max]);
            return $this;
        }

        return $this;
    }

    /**
     * Permet de s'assurer qu'un champ est un slug
     * @param string $field
     * @return self
     */
    public function slug(string $field): self
    {
        $pattern = '/^([a-z0-9]+-?)+$/';
        $value = $this->getValue($field);

        if (!is_null($value) AND !preg_match($pattern, $value)) {
            $this->addError($field, 'slug');
        }

        return $this;
    }

    public function regex(string $field, string $pattern)
    {
        $value = $this->getValue($field);
        if (!is_null($value) AND !preg_match($pattern, $value)) {
            $this->addError($field, 'regex'. [$pattern]);
        }
    }

    public function dateTime(string $field, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($field);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 OR $errors['warning_count'] > 0 OR $date === false) {
            $this->addError($field, 'datetime', [$format]);
        }

        return $this;
    }

    public function setCustomName(string $field, ?string $customName): self
    {
        if (is_null($customName)) {
            unset($this->customNames[$field]);
        } else {
            $this->customNames[$field] = $customName;
        }

        return $this;
    }

    /**
     * Permet de récupérer les erreurs.
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool Vérifie les données
     */
    public function check(): bool
    {
        return empty($this->errors);
    }

    /**
     * Obtient une valeur en fonction du nom du champ, si le champ n'existe pas, retourne null
     * @param string $field
     * @return null|mixed
     */
    private function getValue(string $field)
    {
        if (array_key_exists($field, $this->fields)) {
            return $this->fields[$field];
        }
        return null;
    }

    /**
     * Permet d'instancier et d'ajouter une erreur
     * @param string $field
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $field, string $rule, array $attributes = []): void
    {
        if (!isset($this->errors[$field])) {
            if (array_key_exists($field, $this->customNames)) {
                $fieldName = $this->customNames[$field];
            } else {
                $fieldName = $field;
            }
            $this->errors[$field] = new ValidationError($fieldName, $rule, $attributes);
        }
    }

}