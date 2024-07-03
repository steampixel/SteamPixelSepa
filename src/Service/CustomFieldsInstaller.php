<?php declare(strict_types=1);

namespace SteamPixelSepa\Sepa\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;


class CustomFieldsInstaller
{
    private const CUSTOM_FIELDSET_NAME = 'steam_pixel_sepa';


    private const CUSTOM_FIELDSET_ORDER = [
        'name' => self::CUSTOM_FIELDSET_NAME . '_order',
        'config' => [
            'label' => [
                'en-GB' => 'Steam Pixel SEPA Order Custom Field Set',
                'de-DE' => 'Aktualisierte deutsche Übersetzung',
                Defaults::LANGUAGE_SYSTEM => 'Steam Pixel SEPA Order Custom Field Set'
            ]
        ],
        'customFields' => [
            [
                'name' => 'custom_order_sepa_owner',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Order SEPA Owner',
                        'de-DE' => 'Besteller SEPA Inhaber',
                        Defaults::LANGUAGE_SYSTEM => 'Order SEPA Owner'
                    ],
                    'customFieldPosition' => 1
                ]
            ],
            [
                'name' => 'custom_order_sepa_iban',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Order SEPA IBAN',
                        'de-DE' => 'Besteller SEPA IBAN',
                        Defaults::LANGUAGE_SYSTEM => 'Order SEPA IBAN'
                    ],
                    'customFieldPosition' => 2
                ]
            ],
            [
                'name' => 'custom_order_sepa_bic',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Order SEPA BIC',
                        'de-DE' => 'Besteller SEPA BIC',
                        Defaults::LANGUAGE_SYSTEM => 'Order SEPA BIC'
                    ],
                    'customFieldPosition' => 3
                ]
            ]
        ]
    ];

    private const CUSTOM_FIELDSET_CUSTOMER = [
        'name' => self::CUSTOM_FIELDSET_NAME . '_customer',
        'config' => [
            'label' => [
                'en-GB' => 'Steam Pixel SEPA Customer Custom Field Set',
                'de-DE' => 'Aktualisierte deutsche Übersetzung',
                Defaults::LANGUAGE_SYSTEM => 'Steam Pixel SEPA Customer Custom Field Set'
            ]
        ],
        'customFields' => [
            [
                'name' => 'custom_customer_sepa_owner',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Customer SEPA Owner',
                        'de-DE' => 'Kunde SEPA Inhaber',
                        Defaults::LANGUAGE_SYSTEM => 'Customer SEPA Owner'
                    ],
                    'customFieldPosition' => 1
                ]
            ],
            [
                'name' => 'custom_customer_sepa_iban',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Customer SEPA IBAN',
                        'de-DE' => 'Kunde SEPA IBAN',
                        Defaults::LANGUAGE_SYSTEM => 'Customer SEPA IBAN'
                    ],
                    'customFieldPosition' => 2
                ]
            ],
            [
                'name' => 'custom_customer_sepa_bic',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'label' => [
                        'en-GB' => 'Customer SEPA BIC',
                        'de-DE' => 'Kunde SEPA BIC',
                        Defaults::LANGUAGE_SYSTEM => 'Customer SEPA BIC'
                    ],
                    'customFieldPosition' => 3
                ]
            ]
        ]
    ];





    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldSetRelationRepository
    ) {
    }

    public function install(Context $context): void
    {
        $this->customFieldSetRepository->upsert([
            self::CUSTOM_FIELDSET_ORDER
        ], $context);

        $this->customFieldSetRepository->upsert([
            self::CUSTOM_FIELDSET_CUSTOMER
        ], $context);
    }

    public function uninstall(Context $context): void
    {
        $this->removeRelations($context, 'order');
        $this->removeRelations($context, 'customer');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME . '_order'));
        $this->customFieldSetRepository->delete($criteria, $context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME . '_customer'));
        $this->customFieldSetRepository->delete($criteria, $context);
    }

    public function addRelations(Context $context, $entityName): void
    {
        $this->customFieldSetRelationRepository->upsert(array_map(function (string $customFieldSetId) use ($entityName) {
            return [
                'customFieldSetId' => $customFieldSetId,
                'entityName' => $entityName,
            ];
        }, $this->getCustomFieldSetIds($context,$entityName)), $context);
    }


    public function removeRelations(Context $context, $entityName): void
    {
        $customFieldSetIds = $this->getCustomFieldSetIds($context,$entityName);
        foreach ($customFieldSetIds as $customFieldSetId) {
            $this->customFieldSetRelationRepository->delete([
                'customFieldSetId' => $customFieldSetId,
                'entityName' => $entityName,
            ], $context);
        }
    }


    /**
     * @return string[]
     */
    private function getCustomFieldSetIds(Context $context, $entityName): array
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME . '_' . $entityName));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }
}
