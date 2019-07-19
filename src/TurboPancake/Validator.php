<?php
namespace TurboPancake;

use DateTime;
use TurboPancake\Database\Table;
use TurboPancake\Validator\ValidationError;

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
     *
     * @param string[] $fields
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
     *
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
     * Verifie la longeur d'un champ
     *
     * @param string $field
     * @param int|null $min
     * @param int|null $max
     * @param string|null $customError
     * @return Validator
     * @throws \Exception
     */
    public function length(string $field, ?int $min, ?int $max = null, ?string $customError = null): self
    {
        if (is_null($min) AND is_null($max)) {
            throw new \Exception(
                'Validation error: you need to define a least one length parameter in addition to the field name.',
                E_WARNING
            );
        }
        if (!is_null($min) AND !is_null($max) AND $min > $max) {
            throw new \Exception(
                'Validation error: the max length must be least the value of the min length.',
                E_WARNING
            );
        }

        $value = $this->getValue($field);
        $length = mb_strlen($value);
        if (!is_null($min) AND
            !is_null($max) AND
            ($length < $min OR $length > $max)
        ) {
            $this->addError($field, 'between', [$min, $max], $customError);
            return $this;
        }
        if (!is_null($min) AND
            $length < $min
        ) {
            $this->addError($field, 'short', [$min], $customError);
            return $this;
        }
        if (!is_null($max) AND
            $length > $max
        ) {
            $this->addError($field, 'long', [$max], $customError);
            return $this;
        }

        return $this;
    }

    /**
     * Permet de s'assurer qu'un champ est un slug
     *
     * @param string $field
     * @param string|null $customError
     * @return self
     */
    public function slug(string $field, ?string $customError = null): self
    {
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        $value = $this->getValue($field);

        if (!is_null($value) AND !preg_match($pattern, $value)) {
            $this->addError($field, 'slug', [], $customError);
        }

        return $this;
    }

    /**
     * Permet de s'assurer qu'un champ est un timestamp
     *
     * @param string $field
     * @param string $format
     * @param string|null $customError
     * @return Validator
     */
    public function dateTime(string $field, string $format = 'Y-m-d H:i:s', ?string $customError = null): self
    {
        $value = $this->getValue($field);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 OR $errors['warning_count'] > 0 OR $date === false) {
            $this->addError($field, 'datetime', [$format], $customError);
        }

        return $this;
    }

    /**
     * Verifie si un champ correspond a une regex
     *
     * @param string $field
     * @param string $pattern
     * @param string|null $customError
     * @return self
     */
    public function regex(string $field, string $pattern, ?string $customError = null): self
    {
        $value = $this->getValue($field);
        if (!is_null($value) AND !preg_match($pattern, $value)) {
            $this->addError($field, 'regex', [$pattern], $customError);
        }
        return $this;
    }

    /**
     * Vérifie qu'un id existe dans un instance donnée de Table ou dans un tableau
     *
     * @param string $field
     * @param Table|array $dataSource
     * @param string|null $customError
     * @return Validator
     * @throws \Exception
     */
    public function exists(string $field, $dataSource, ?string $customError = null): self
    {
        $value = $this->getValue($field);
        if ($dataSource instanceof Table) {
            if (!$dataSource->exists($value)) {
                $this->addError($field, 'exists', [], $customError);
            }
        } elseif (is_array($dataSource)) {
            if (!in_array($value, $dataSource)) {
                $this->addError($field, 'exists', [], $customError);
            }
        } else {
            throw new \Exception(
                'Validation error: the $dataSource parameters must be an array or a Table instance.',
                E_WARNING
            );
        }
        return $this;
    }

    /**
     *
     * Vérifie qu'un élément est unique dans un instance donnée de Table ou dans un tableau
     *
     * @param string $field
     * @param Table|array $dataSource
     * @param string $column
     * @param array|null $exclude
     * @param string|null $customError
     * @return Validator
     * @throws \Exception
     */
    public function unique(
        string $field,
        $dataSource,
        string $column = null,
        ?array $exclude = null,
        ?string $customError = null
    ): self {
        $value = $this->getValue($field);
        if (!is_null($exclude) AND in_array($value, $exclude)) {
            return $this;
        }

        if ($dataSource instanceof Table and !is_null($column)) {
            if (count($dataSource->findBy($column, $value)) > 0) {
                $this->addError($field, 'unique', [$value], $customError);
            }
        } elseif (is_array($dataSource)) {
            if (in_array($value, $dataSource)) {
                $this->addError($field, 'unique', [$value], $customError);
            }
        } else {
            throw new \Exception(
                'Validation error: the $dataSource parameters must be an array or a Table instance.',
                E_WARNING
            );
        }
        return $this;
    }

    /**
     * Définit un nom d'affichage pour un champ
     *
     * @param string $field
     * @param string|null $customName
     * @return Validator
     */
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
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Renvoie le status de vérification
     *
     * @return bool
     */
    public function check(): bool
    {
        return empty($this->errors);
    }

    /**
     * Obtient une valeur en fonction du nom du champ, si le champ n'existe pas, retourne null
     *
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
     *
     * @param string $field
     * @param string $rule
     * @param array $attributes
     * @param string|null $customError
     */
    private function addError(string $field, string $rule, array $attributes = [], ?string $customError = null): void
    {
        if (!isset($this->errors[$field])) {
            if (array_key_exists($field, $this->customNames)) {
                $fieldName = $this->customNames[$field];
            } else {
                $fieldName = $field;
            }
            $this->errors[$field] = new ValidationError($fieldName, $rule, $attributes, $customError);
        }
    }

}
