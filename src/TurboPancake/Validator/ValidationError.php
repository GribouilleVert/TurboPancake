<?php
namespace TurboPancake\Validator;

class ValidationError {

    private $field;
    private $rule;
    private $attributes;
    private $customError = null;

    private $messages = [
        'required'          => 'Le champ %s est requis.',
        'empty'             => 'Le champ %s ne doit pas être vide.',
        'between'           => 'Le champ %s doit faire entre %d et %d caractères.',
        'short'             => 'Le champ %s doit faire au moins %d caractères.',
        'long'              => 'Le champ %s doit faire moins de %d caractères.',
        'slug'              => 'Le champ %s n\'est pas une URL valide.',
        'regex'             => 'Le champ %s ne valide pas l\'expression régulière "%s".',
        'datetime'          => 'Le champ %s doit correspondre au format %s.',
        'exists'            => 'La valeur de %s que vous avez selectionée n\'existe pas.',
        'unique'            => 'Le champ %s ne peut pas avoir la valeur "%s" car elle est déja utilisée',
        'mimes'             => 'Le fichier %s que vous avez envoyé doit être parmis les types mimes suivants: %s.',
        'regexMime'         => 'Le fichier %s que vous avez envoyé doit être de type mime compatible avec cette expression régulière: %s.',
        'dimensions'        => 'L\'image %s doit faire au moins %dpx par %dpx',
        'dotfile'           => 'Désolé mais vous ne pouvez pas envoyer de dotfile (fichiers sans extenstion commençant par un point).',
        'extensionMismatch' => 'L\'extension de votre fichier ne correspond pas à son contenu, pour des raisons de sécurité il à été refusé.',
        'notUploaded'       => 'Le fichier est requis.',
    ];

    public function __construct(string $field, string $rule, array $attributes = [], ?string $customError = null)
    {
        $this->field = $field;
        $this->rule = $rule;
        $this->attributes = $attributes;
        if (!is_null($customError)) {
            $this->customError = $customError;
        }
    }

    public function __toString(): string
    {
        $params = array_merge([$this->customError??$this->messages[$this->rule], $this->field], $this->attributes);
        return call_user_func_array('sprintf', $params);
    }

}
