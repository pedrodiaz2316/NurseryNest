<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Core\View;
use NurseryNest\Repository\NurseryRepository;

/**
 * Public marketing site (WEB-1 to WEB-5).
 */
final class PublicController
{
    public function __construct(private readonly NurseryRepository $nurseries)
    {
    }

    public function home(): void
    {
        View::render('public/home', [
            'title'        => 'Florida\'s home for plant nurseries',
            'nurseryCount' => $this->nurseries->countActive(),
            'featured'     => array_slice($this->nurseries->allActive(), 0, 3),
        ]);
    }

    public function about(): void
    {
        View::render('public/about', ['title' => 'About us']);
    }

    public function specialty(): void
    {
        View::render('public/specialty', ['title' => 'Our specialty']);
    }

    public function features(): void
    {
        View::render('public/features', ['title' => 'Features']);
    }

    public function nurseries(): void
    {
        View::render('public/nurseries', [
            'title'     => 'Partner nurseries',
            'nurseries' => $this->nurseries->allActive(),
        ]);
    }

    public function contact(): void
    {
        View::render('public/contact', ['title' => 'Contact']);
    }
}
