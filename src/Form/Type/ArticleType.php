<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Form\Type;

use Maximus\Entity\Article;
use Maximus\Entity\Author;
use Maximus\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ArticleType
 */
class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('alias', TextType::class, ['label' => 'Alias', 'help' => 'Alias name accept only English characters or a dash symbol ("-"), ex: this-is-an-alias'])
            ->add('tags', MultipleChoiceType::class, ['label' => 'Tags', 'class' => Tag::class, 'attr' => ['multiple' => true]])
            ->add('author', EntityType::class, ['label' => 'Author', 'choice_label' => 'name', 'class' => Author::class, 'placeholder' => 'Choose an author name'])
            ->add('published', ChoiceType::class, ['label' => 'Published', 'choices' => ['Draft' => 0, 'Published' => 1]])
            ->add('markdownContent', TextareaType::class, ['label' => 'Content', 'attr' => ['placeholder' => 'Write a content or drag your files here...']])
            ->add('backgroundImagePath', FileType::class, ['label' => 'Background', 'required' => false, 'attr' => ['accept' => '.jpg,.jpeg,.png']])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
