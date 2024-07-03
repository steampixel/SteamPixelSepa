<?php declare(strict_types=1);

namespace SteamPixelSepa\Sepa;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use SteamPixelSepa\Sepa\Service\CustomFieldsInstaller;

class Sepa extends Plugin
{

        public function install(InstallContext $installContext): void
        {
            // Do stuff such as creating a new payment method
    
            $this->getCustomFieldsInstaller()->install($installContext->getContext());
        }
    
        public function uninstall(UninstallContext $uninstallContext): void
        {
            parent::uninstall($uninstallContext);
    
            if ($uninstallContext->keepUserData()) {
                return;
            }
    
            $this->getCustomFieldsInstaller()->uninstall($uninstallContext->getContext());
        }
    
        public function activate(ActivateContext $activateContext): void
        {
       
            $this->getCustomFieldsInstaller()->addRelations($activateContext->getContext(),'order');
            $this->getCustomFieldsInstaller()->addRelations($activateContext->getContext(),'customer');
        }
    
        public function deactivate(DeactivateContext $deactivateContext): void
        {

            $this->getCustomFieldsInstaller()->removeRelations($deactivateContext->getContext(), 'order');
            $this->getCustomFieldsInstaller()->removeRelations($deactivateContext->getContext(), 'customer');
    
        }
    
        public function update(UpdateContext $updateContext): void
        {
            // Update necessary stuff, mostly non-database related
        }
    
        public function postInstall(InstallContext $installContext): void
        {
        }
    
        public function postUpdate(UpdateContext $updateContext): void
        {
        }
    
        private function getCustomFieldsInstaller(): CustomFieldsInstaller
        {
            if ($this->container->has(CustomFieldsInstaller::class)) {
                return $this->container->get(CustomFieldsInstaller::class);
            }
    
            return new CustomFieldsInstaller(
                $this->container->get('custom_field_set.repository'),
                $this->container->get('custom_field_set_relation.repository')
            );
        }
}
