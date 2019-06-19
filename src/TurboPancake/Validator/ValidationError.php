<?php
namespace TurboPancake\Validator;

class ValidationError {

    private $field;
    private $rule;
    private $attributes;

    private $messages = [
        'required'  => 'Le champ %s est requis.',
        'empty'     => 'Le champ %s ne doit pas être vide.',
        'between'   => 'Le champ %s doit faire entre %d et %d caractères.',
        'short'     => 'Le champ %s doit faire au moins %d caractères.',
        'long'      => 'Le champ %s doit faire moins de %d caractères.',
        'slug'      => 'Le champ %s n\'est pas une URL valide.',
        'regex'     => 'Le champ %s ne valide pas l\'expression régulière "%s"',
        'datetime'  => 'Le champ %s doit correspondre au format %s'
    ];

    public function __construct(string $field, string $rule, array $attributes = [])
    {
        $this->field = $field;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString(): string
    {
        $params = array_merge([$this->messages[$this->rule], $this->field], $this->attributes);
        return call_user_func_array('sprintf', $params);
    }

}