<?php

namespace AppBundle\Controller;

use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Articles;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ArticleController extends FOSRestController
{
    /**
     * @Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post("/articles")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter(
     *     "article",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     */
    public function createAction(Article $article, ConstraintViolationList $violations)
    {

        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        return $this->view($article, Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('app_article_show',
                ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]),
        ]);
    }
// Exemple utilisant le formulaire au lieu du body converter
//    /**
//     * @Rest\Post(
//     *    path = "/articles",
//     *    name = "app_article_create"
//     * )
//     * @Rest\View(StatusCode = 201)
//     */
//    public function createAction()
//    {
//        $data = $this->get('jms_serializer')->deserialize($request->getContent(), 'array', 'json');
//        $article = new Article;
//        $form = $this->get('form.factory')->create(ArticleType::class, $article);
//        $form->submit($data);
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($article);
//        $em->flush();
//
//        return $this->view($article, Response::HTTP_CREATED, ['Location' => $this->generateUrl('app_article_show', ['id' => $article->getId()])]);
//    }


    /**
     * @Rest\Delete(
     *     path="/articles/{id}",
     *     name="app_article_delete",
     *     requirements={"id"="\d+"}
     *       )
     * @Rest\View(StatusCode=204)
     */
    public function deleteAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
    }

    /**
     * @Rest\Put(
     *     path="/articles/{id}",
     *     name="app_article_edit",
     *     requirements={"id"="\d+"}
     *     )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter(
     *     "newArticle",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     */
    public function editArticle(Article $article, Article $newArticle, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $article->setTitle($newArticle->getTitle());
        $article->setContent($newArticle->getContent());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->view($article, Response::HTTP_OK );
    }

    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }
}