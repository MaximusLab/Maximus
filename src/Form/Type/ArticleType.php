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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('alias', TextType::class, [
                'label' => 'Alias',
                'help' => 'Alias name accept only lowercase English characters and dash ("-"), e.g.: this-is-an-alias',
                'required' => false,
            ])
            ->add('docUrl', TextType::class, [
                'label' => 'Document URL',
                'required' => false,
            ])
            ->add('tags', MultipleChoiceType::class, ['label' => 'Tags', 'class' => Tag::class, 'attr' => ['multiple' => true]])
            ->add('author', EntityType::class, ['label' => 'Author', 'choice_label' => 'name', 'class' => Author::class, 'placeholder' => 'Choose an author name'])
            ->add('published', ChoiceType::class, ['label' => 'Published', 'choices' => ['Draft' => 0, 'Published' => 1]])
            ->add('markdownContent', TextareaType::class, ['label' => 'Content', 'attr' => ['placeholder' => 'Write a content or drag your files here...']])
            ->add('backgroundImagePath', FileType::class, [
                'label' => 'Background',
                'required' => false,
                'attr' => [
                    'accept' => Article::FILE_INPUT_ATTR_ACCEPT,
                ],
                'data_class' => null,
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => Article::VALID_UPLOAD_MIME_TYPES,
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $article = $event->getData();
            $form = $event->getForm();

            if ($article instanceof Article && !empty($article->getId()) && $article->getPublished()) {
                $form->add('publishedAt', DateTimeType::class, ['label' => 'Published at', 'widget' => 'single_text', 'format' => 'Y/MM/dd HH:mm:ss']);
            }
        });
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
