<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2021, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

class Schema {
    private $schemas = [];
    private $faqs    = [];

    public function add(array $data): void {
        $this->schemas[] = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function output(): string {
        if ($this->faqs) {
            $this->add(Schema::FAQPage($this->faqs));
        }

        return '<script type="application/ld+json">' . implode('</script><script type="application/ld+json">', $this->schemas) . '</script>';
    }

    public function addFAQ(string $question, string $answer): void {
        $this->faqs[$question] = array('question' => $question, 'answer' => $answer);
    }

	static public function breadcrumb(array $breadcrumbs): array {
        $itemListElement = array();

        foreach (array_values($breadcrumbs) as $key => $value) {
            if($key === 0 && $value['text'][0] === '<'){
                $name = Registry::get('language')->get('text_home_text');
            } else {
                $name = $value['text'];
            }

            $listItem = array(
                '@type' => 'ListItem',
                'position' => $key + 1,
                'name' => $name,
            );

            if($key !== (count($breadcrumbs) - 1)){
                $listItem['item'] = $value['href'];
            }

            $itemListElement[] = $listItem;
        }

        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        );
    }

    static public function organization(): array {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'url' => Registry::config('mz_store_url'),
            'name' => Registry::config('config_name'),
            'logo' => getImageUrl(Registry::config('config_logo')),
            'contactPoint' => [
                [
                    '@type' => 'ContactPoint',
                    'telephone' => Registry::config('config_telephone'),
                    'email' => Registry::config('config_email'),
                    'contactType' => Registry::get('language')->get('text_customer_service'),
                ]
            ]
        );
    }

    static public function website(): array {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => Registry::config('mz_store_url'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => urldecode(Registry::get('url')->link('product/search', 'search={search_term_string}')),
                'query' => 'required',
                'query-input' => 'required name=search_term_string',
            ]
        );
    }

    static public function FAQPage(array $faqs): array {
        return array(
            '@context' => 'http://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function(array $faq) {
                return [
                    '@type'             => 'Question',
                    'name'              => $faq['question'],
                    'acceptedAnswer'    => [
                        '@type' => 'Answer',
                        'text'  => $faq['answer'],
                    ],
                ];
            }, array_values($faqs)),
        );
    }
}
