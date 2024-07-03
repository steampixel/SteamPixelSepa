<?php declare(strict_types=1);

/*
  This controller will override the frontend.account.payment.save controller.
  It will now catch the SEPA data and will store it to the customer if
  the customer changes the fields inside the account information
*/

// Use your own plugin namespace
namespace SteamPixelSepa\Sepa\Storefront\Controller;

// Use this shopware classes
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractChangePaymentMethodRoute;
use Shopware\Core\Checkout\Payment\Exception\UnknownPaymentMethodException;
use Shopware\Core\Checkout\Payment\PaymentException;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Exception\InvalidUuidException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Account\PaymentMethod\AccountPaymentMethodPageLoadedHook;
use Shopware\Storefront\Page\Account\PaymentMethod\AccountPaymentMethodPageLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]
class AccountPaymentController extends StorefrontController
{

    public function __construct(
        private readonly EntityRepository $customerRepository,
        private readonly AccountPaymentMethodPageLoader $paymentMethodPageLoader,
        private readonly AbstractChangePaymentMethodRoute $changePaymentMethodRoute
    ) {
    }


    #[Route(path: '/account/payment', name: 'frontend.account.payment.save', defaults: ['_loginRequired' => true], methods: ['POST'])]
    public function savePayment(RequestDataBag $requestDataBag, SalesChannelContext $context, ?CustomerEntity $customer = null): Response
    {

        try {
            $paymentMethodId = $requestDataBag->getAlnum('paymentMethodId');

            $this->changePaymentMethodRoute->change(
                $paymentMethodId,
                $requestDataBag,
                $context,
                $customer
            );
        } catch (InvalidUuidException|PaymentException $exception) {
            $this->addFlash(self::DANGER, $this->trans('error.' . $exception->getErrorCode()));

            return $this->forwardToRoute('frontend.account.payment.page', ['success' => false]);
        }

        $this->addFlash(self::SUCCESS, $this->trans('account.paymentSuccess'));

        // Write the SEPA data to the customer entity

        // Get the post data
        $sepa_store = (isset($_POST['sepa_store'])?$_POST['sepa_store']:false);
        $sepa_owner = (isset($_POST['sepa_owner'])?$_POST['sepa_owner']:false);
        $sepa_iban = (isset($_POST['sepa_iban'])?$_POST['sepa_iban']:false);
        $sepa_bic = (isset($_POST['sepa_bic'])?$_POST['sepa_bic']:false);
        $sepa_already = (isset($_POST['sepa_already'])?true:false);

        if($sepa_store !== false) {

          // Store the data to the customer account
          $this->customerRepository->upsert([
              [
                  'id' => $customer->getId(),
                  'customFields' => [
                    'custom_customer_sepa_owner' => $sepa_owner,
                    'custom_customer_sepa_iban' => $sepa_iban,
                    'custom_customer_sepa_bic' => $sepa_bic,
                    'custom_customer_sepa_already' => $sepa_already
                  ]
              ],
          ], $context->getContext());

        }

        return new RedirectResponse($this->generateUrl('frontend.account.payment.page'));
    }
}
