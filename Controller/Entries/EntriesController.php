<?php

	namespace ReaccionEstudio\ReaccionCMSBundle\Controller\Entries;

	use Symfony\Component\HttpFoundation\Request;
	use ReaccionEstudio\ReaccionCMSBundle\Entity\Entry;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;

	class EntriesController extends Controller
	{
		/**
		 * Blog home - Entries list
		 */
		// TODO: generate SEO title
		public function index(Request $request, Int $page = 1)
		{
			$entriesService = $this->get("reaccion_cms.entries");
			$view = $this->get("reaccion_cms.theme")->getConfigView("entries", true);

			// get data
			$entries 	= $entriesService->getEntries('en', $page);
			$categories = $this->get("reaccion_cms.entries")->getCategories();

			// view vars
			$vars = [ 
						'seo' => [], 
						'name' => 'Blog',
						'entries' => $entries,
						'categories' => $categories,
						'type' => 'entry'
					];

			return $this->render($view, $vars);
		}

		/**
		 * Blog - List entries filtering by category
		 */
		public function category(Request $request, String $category="", Int $page = 1)
		{
			$currentCategoryEntity = null;
			$em = $this->getDoctrine()->getManager();
			$entriesService = $this->get("reaccion_cms.entries");
			$view = $this->get("reaccion_cms.theme")->getConfigView("entries", true);

			// get categories
			$categories = $this->get("reaccion_cms.entries")->getCategories();

			// get current category entity
			foreach($categories as $categoryEntity)
			{
				if($categoryEntity->getSlug() == $category)
				{
					$currentCategoryEntity = $categoryEntity;
				}
			}

			// get entries
			$entriesFilters = ['categories' => [ $category ] ];
			$entries = $em->getRepository(Entry::class)->getEntries($entriesFilters);

			// entries pagination
			$paginator = $this->get('knp_paginator');
		    $entries = $paginator->paginate(
		        $entries,
		        $page,
		        $this->getParameter("pagination_page_limit")
		    );

			// view vars
			$vars = [ 
						'seo' => [], 
						'name' => 'Blog',
						'entries' => $entries,
						'categories' => $categories,
						'currentCategory' => $currentCategoryEntity,
						'type' => 'entry'
					];

			return $this->render($view, $vars);
		}

		/**
		 * Blog - List entries filtering by tag
		 */
		public function tag(Request $request, String $tag="", Int $page = 1)
		{
			$currentCategoryEntity = null;
			$em = $this->getDoctrine()->getManager();
			$entriesService = $this->get("reaccion_cms.entries");
			$view = $this->get("reaccion_cms.theme")->getConfigView("entries", true);

			// get categories
			$categories = $this->get("reaccion_cms.entries")->getCategories();

			// get entries
			$entriesFilters = ['tag' => $tag ];
			$entries = $em->getRepository(Entry::class)->getEntries($entriesFilters);

			// entries pagination
			$paginator = $this->get('knp_paginator');
		    $entries = $paginator->paginate(
		        $entries,
		        $page,
		        $this->getParameter("pagination_page_limit")
		    );

			// view vars
			$vars = [ 
						'seo' => [], 
						'name' => 'Blog',
						'entries' => $entries,
						'categories' => $categories,
						'currentTag' => $tag,
						'type' => 'entry'
					];

			return $this->render($view, $vars);
		}
	}