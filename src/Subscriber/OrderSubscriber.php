<?php declare(strict_types=1);

/*
  This subscriber will listen to new incomming orders
  In case a new order is created it will catch the SEPA data and will append it to the order entity.
  Also it will store this data to the customer account.
*/

namespace SteamPixelSepa\Sepa\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class OrderSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        EntityRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
          CartConvertedEvent::class => 'addCustomFieldsToConvertedCart',
        ];
    }

    public function addCustomFieldsToConvertedCart(CartConvertedEvent $event)
    {
      $sepa_store = (isset($_POST['sepa_store'])?$_POST['sepa_store']:false);
      $sepa_owner = (isset($_POST['sepa_owner'])?$_POST['sepa_owner']:false);
      $sepa_iban = (isset($_POST['sepa_iban'])?$_POST['sepa_iban']:false);
      $sepa_bic = (isset($_POST['sepa_bic'])?$_POST['sepa_bic']:false);
      $sepa_already = (isset($_POST['sepa_already'])?true:false);

      if($sepa_store !== false) {

        $cart = $event->getConvertedCart();
        $cart['customFields']['custom_order_sepa_owner'] = $sepa_owner;
        $cart['customFields']['custom_order_sepa_iban'] = $sepa_iban;
        $cart['customFields']['custom_order_sepa_bic'] = $sepa_bic;
        $cart['customFields']['custom_order_sepa_already'] = $sepa_already;
        $event->setConvertedCart($cart);

        $this->customerRepository->upsert([
            [
                'id' => $cart['orderCustomer']['customer']['id'],
                'customFields' => [
                  'custom_customer_sepa_owner' => $sepa_owner,
                  'custom_customer_sepa_iban' => $sepa_iban,
                  'custom_customer_sepa_bic' => $sepa_bic,
                  'custom_customer_sepa_already' => $sepa_already
                ]
            ],
        ], $event->getContext());

      }

    }
}
