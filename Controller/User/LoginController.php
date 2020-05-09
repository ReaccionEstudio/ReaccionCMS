<?php

	namespace ReaccionEstudio\ReaccionCMSBundle\Controller\User;

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Translation\TranslatorInterface;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use ReaccionEstudio\ReaccionCMSBundle\Constants\Cookies;
	use ReaccionEstudio\ReaccionCMSBundle\Form\Users\UserSettingsType;
	use ReaccionEstudio\ReaccionCMSBundle\Form\Users\UserRegisterType;
	use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
	use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
	use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

	class LoginController extends Controller
	{
		/**
		 * @var TranslatorInterface
		 */
		private $translator;

		/**
		 * @var EncoderFactoryInterface
		 */
		private $encoder;

		/**
		 * Construct
		 */
		public function __construct(TranslatorInterface $translator, EncoderFactoryInterface $encoder)
		{
			$this->translator 	= $translator;
			$this->encoder 		= $encoder;
		}

		/**
		 * Login existing user
		 */
		public function index(Request $request)
		{
			// If user is already logged in, redirect to home
			if( ! empty($this->getUser()) )
			{
				return $this->get("reaccion_cms.user")->redirect('login_route_user_already_logged');
			}
			
			$seo = ['title' => $this->translator->trans("signin.title") ];

			// form
			$form = $this->createForm(UserSettingsType::class);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) 
			{
				$username 	= $form['username']->getData();
				$password 	= $form['password']->getData();

				// get user manager
				$userManager = $this->get('fos_user.user_manager');
				$user = $userManager->findUserByUsernameOrEmail($username);

				if( ! empty($user))
				{
					// check credentials
	    			$isValidPassword = $this->encoder->getEncoder($user)->isPasswordValid($user->getPassword(), $password, $user->getSalt());

	    			if($isValidPassword)
	    			{
	    				$this->get("reaccion_cms.authentication")->setUser($user)->authenticate(true);
	    				return $this->get("reaccion_cms.user")->redirect('user_login_successful');
	  	  			}
	    			else
	    			{
	    				$this->addFlash('signin_error', $this->translator->trans('signin.invalid_credentials'));
	    			}
				}
				else
				{
	    			$this->addFlash('signin_error', $this->translator->trans('signin.user_doesnt_exists', ['%username%' => $username]));
				}
			}

			// view
			$view = $this->get("reaccion_cms.theme")->getConfigView("login", true);
			$vars = [
				'form' 		=> $form->createView(),
				'seo' 		=> $seo
			];

			return $this->render($view, $vars);
		}
	}
