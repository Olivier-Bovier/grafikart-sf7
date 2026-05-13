<?php

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormListenerFactory
{
    
    public function __construct(private SluggerInterface $slugger) {}

    public function autoSlug(String $field): callable
    {
        return function(PreSubmitEvent $event) use ($field) {
            $data = $event->getData();
            if (empty($data['slug'])) {
                $slugger = new AsciiSlugger();
                $data['slug'] = $this->slugger->slug($data[$field])->lower();
                $event->setData($data);
            }
        };

    }

    public function timestamps() {

        return function(PostSubmitEvent $event) {
             $data = $event->getData();

             $data->setUpdatedAt(new \DateTimeImmutable());
             if (method_exists($data, 'getId') && !$data->getId() && method_exists($data, 'setCreatedAt')) {
                 $data->setCreatedAt(new \DateTimeImmutable());
             }
        };
    }
}