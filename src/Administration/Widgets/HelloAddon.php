<?php
namespace TurboModule\Administration\Widgets;

use TurboModule\Administration\AdminAddonInterface;

class HelloAddon implements AdminAddonInterface {

    public function renderWidget(): string
    {
        return <<<HTML
        <div class="card">
            <div class="card-header">
            <div class="card-title h5">Salut la planète !</div>
            </div>
            <div class="card-footer">
                <div class="text-gray">Ceci est un module d'exemple</div>
            </div>
        </div>
        HTML;
    }

    public function renderMenuItem(): ?string
    {
        return null;
    }

}
