<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @ORM\Table()
 *
 * @ExclusionPolicy("all")
 *
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_article_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "edit",
 *      href = @Hateoas\Route(
 *          "app_article_edit",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_article_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 *
 * @Hateoas\Relation(
 *     "author",
 *     embedded = @Hateoas\Embedded("expr(object.getAuthor())")
 * )
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Expose
     * @Serializer\Since("1.0")
     *
     * @Assert\NotBlank(groups={"Create"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Expose
     * @Serializer\Since("1.0")
     *
     * @Assert\NotBlank(groups={"Create"})
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * @Serializer\Since("2.0")
     */
    private $shortDescription;

    /**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"all"}, fetch="EAGER")
     */
    private $author;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param mixed $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }
}