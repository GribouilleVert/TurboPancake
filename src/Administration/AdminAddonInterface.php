<?php
namespace TurboModule\Administration;

interface AdminAddonInterface {

    public function renderWidget(): ?string;

    public function renderMenuItem(): ?string;

}
