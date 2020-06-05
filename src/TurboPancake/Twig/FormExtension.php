<?php
namespace TurboPancake\Twig;

use TurboPancake\Validator\ValidationError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FormExtension extends AbstractExtension {

    private $registeredId = [];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * Permet de génerer un champs
     * @param $context
     * @param string $name Nom du champs dans $_POST/$_GET
     * @param null|string $label Titre du champ
     * @param null|mixed $value Valeur du champs
     * @param array $options Options
     * @return string|null
     */
    public function field($context, string $name, ?string $label = null, $value = null, array $options = [])
    {
        $options = array_merge([
            'type' => 'text',
            'attributes' => [],
            'input_classes' => [],
            'wrapper_classes' => [],
            'lock_id' => false,
            'placeholder' => null,
            'extra_html' => null,
            'error' => $context['errors'][$name] ?? false,
            //Select specific options
            'select_options' => [],
            //Checkboxe specific options
            'lever' => false,
        ], $options);

        $id = $this->generateId($name, $options['lock_id']);

        $value = $this->convertValue($value);

        $errorHtml = $this->getErrorHtml($options['error']);
        if ($options['error']) {
            $options['wrapper_classes'][] = 'has-error';
        }

        $classes = $this->buildClasses($options['input_classes']);
        $attributes = [
            'class' => $classes,
            'id'    => $id,
            'name'  => $name,
        ];
        $attributes = array_merge($options['attributes'], $attributes);
        if (!is_null($options['placeholder'])) {
            $attributes['placeholder'] = htmlspecialchars($options['placeholder']);
        }

        $type = $options['type'];
        switch ($type) {
            case 'text':
            case 'email':
            case 'password':
            case 'date':
            case 'time':
            case 'file':
                $attributes['type'] = $type;
                $attributes['value'] = $value;
                $input = $this->input($label, $attributes);
                break;

            case 'file':
                $attributes['type'] = $type;
                $input = $this->file($label, $attributes);
                break;

            case 'textarea':
                $input = $this->textarea($label, $attributes, $value);
                break;

            case 'select':
                if (empty($options['select_options'])) {
                    return null;
                }
                $input = $this->select($label, $attributes, $value, $options['select_options']);
                break;

            case 'checkbox':
                $input = $this->checkbox($label, $attributes, $value, $options['lever']);
                break;

            default:
                return null;
        }

        $wrapperClasses = $this->buildClasses($options['wrapper_classes']);

        $wrapper  = <<<EOT
            <div class="form-group$wrapperClasses">
                $input
                $errorHtml
                {$options['extra_html']}
            </div>
        EOT;

        return $wrapper;
    }

    private function generateId(string $name, bool $ignor_generation = false): string
    {
        if ($ignor_generation) {
            $id = $name;
        } else {
            $id = $name;
            $x = 1;
            while (in_array($id, $this->registeredId)) {
                $id = $name . '-' . $x;
                $x++;
            }
            array_push($this->registeredId, $id);
        }
        return $id;
    }

    private function buildClasses(array $classes): string
    {
        $classesString = '';
        foreach ($classes as $class) {
            $classesString .= " $class";
        }
        return $classesString;
    }

    private function buildAttributes(array $attributes): string
    {
        $attributesString = '';
        foreach ($attributes as $attribute => $attrValue) {
            $attributesString .= " $attribute=\"$attrValue\"";
        }
        return substr($attributesString, 1);
    }

    private function getErrorHtml($error): string
    {
        if ($error AND ($error instanceof ValidationError or gettype($error) === 'string')) {
            $errorHtml = "<p class=\"form-input-hint\">$error</p>";
            return $errorHtml;
        }
        return '';
    }

    private function convertValue($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }

    /**
     * Génère une entrée classique
     *
     * @param null|string $label
     * @param array $attributes
     * @return string
     */
    private function input(?string $label, array $attributes)
    {
        if (!is_null($label)) {
            $id = $attributes['id'] ?? '#';
            $label = "<label class=\"form-label\" for=\"$id\">$label</label>";
        } else {
            $label = '';
        }

        $attributes['class'] = 'form-input' . $attributes['class'];
        $attributesString = $this->buildAttributes($attributes);

        $input = "<input $attributesString>";

        return $label . $input;
    }

    /**
     * Génère une entrée pour fichier
     *
     * @param null|string $label
     * @param array $attributes
     * @return string
     */
    private function file(?string $label, array $attributes)
    {
        if (!is_null($label)) {
            $id = $attributes['id'] ?? '#';
            $label = "<label class=\"form-label\" for=\"$id\">$label</label>";
        } else {
            $label = '';
        }

        $attributes['class'] = 'form-input' . $attributes['class'];
        $attributesString = $this->buildAttributes($attributes);

        $input = "<input $attributesString>";

        return $label . $input;
    }

    /**
     * Génère une textarea
     *
     * @param null|string $label
     * @param array $attributes
     * @param $value
     * @return string
     */
    private function textarea(?string $label, array $attributes, $value)
    {
        if (!is_null($label)) {
            $id = $attributes['id'] ?? '#';
            $label = "<label class=\"form-label\" for=\"$id\">$label</label>";
        } else {
            $label = '';
        }

        $attributes['class'] = 'form-input' . $attributes['class'];
        $attributesString = $this->buildAttributes($attributes);

        $input = "<textarea $attributesString>$value</textarea>";

        return $label . $input;
    }

    /**
     * Génère un select
     *
     * @param null|string $label
     * @param array $attributes
     * @param $value
     * @return string
     */
    private function select(?string $label, array $attributes, $value, $options)
    {
        if (!is_null($label)) {
            $id = $attributes['id'] ?? '#';
            $label = "<label class=\"form-label\" for=\"$id\">$label</label>";
        } else {
            $label = '';
        }

        $attributes['class'] = 'form-select' . $attributes['class'];
        $attributesString = $this->buildAttributes($attributes);

        $optionsString = '';
        foreach ($options as $optionValue => $text) {
            $selected = ($optionValue == $value ? 'selected' : '');
            $optionsString .= "<option value='{$optionValue}' $selected>{$text}</option>";
        }

        $input = "<select $attributesString>$optionsString</select>";

        return $label . $input;
    }

    /**
     * Génère une checkbox ou un levier
     *
     * @param null|string $label
     * @param array $attributes
     * @param bool $checked
     * @param bool $isLever
     * @return string
     */
    private function checkbox(?string $label, array $attributes, bool $checked, bool $isLever)
    {
        if (!is_null($label)) {
            $id = $attributes['id'] ?? '#';
            $label = "<i class=\"form-icon\"></i> $label";
        } else {
            $label = "<i class=\"form-icon\"></i> {$attributes['name']} ?";
        }

        $attributes['type'] = 'checkbox';
        if ($checked) {
            $attributes['checked'] = null;
        }
        $attributesString = $this->buildAttributes($attributes);

        $input = "<input $attributesString>";

        $mode = 'form-' . ($isLever ? 'switch' : 'checkbox');
        $res  = "<label class='$mode' for='$id'>";
        $res .= $input . $label;
        $res .= '';
        return $res;
    }
}
