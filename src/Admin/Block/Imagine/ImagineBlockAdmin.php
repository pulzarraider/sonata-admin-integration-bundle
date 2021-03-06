<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Admin\Block\Imagine;

use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Admin\Block\AbstractBlockAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ImagineBlock;
use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author Horner
 */
class ImagineBlockAdmin extends AbstractBlockAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // image is only required when creating a new item
        // TODO: sonata is not using one admin instance per object, so this doesn't really work - https://github.com/symfony-cmf/BlockBundle/issues/151
        $imageRequired = ($this->getSubject() && $this->getSubject()->getParentDocument()) ? false : true;

        if (null === $this->getParentFieldDescription()) {
            parent::configureFormFields($formMapper);
        }

        $formMapper
            ->tab('form.tab_general')
                ->with('form.group_block', null === $this->getParentFieldDescription()
                    ? ['class' => 'col-md-9']
                    : []
                )
                    ->add('label', TextType::class, array('required' => false))
                    ->add('linkUrl', TextType::class, array('required' => false))
                    ->add('filter', TextType::class, array('required' => false))
                    ->add('image', ImageType::class, array('required' => $imageRequired))
                    ->add('position', HiddenType::class, array('mapped' => false))
                ->end()
            ->end()
        ;
    }

    public function toString($object)
    {
        return $object instanceof ImagineBlock && $object->getLabel()
            ? $object->getLabel()
            : parent::toString($object)
        ;
    }
}
