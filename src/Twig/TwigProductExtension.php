<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigProductExtension extends AbstractExtension 
{
    public function getFilters() 
    {
        return 
        [
            new TwigFilter('price_format', [$this, 'priceFormatFilter'], ['is_safe' => ['html']]),
            new TwigFilter('tva', [$this, 'calculTvaFilter'], ['is_safe' => ['html']]),
            new TwigFilter('promo', [$this, 'getAmountReduced']),
            new TwigFilter('montantTva', [$this, 'calculMontantTvaFilter'], ['is_safe' => ['html']]),
            new TwigFilter('keywords', [$this, 'createKeywordsFilter']),
        ];
    }

    public function priceFormatFilter($price, $decimal_places, $decimal_separator, $thousand_separator) 
    {
        $unit = number_format( intval($price), 0, $decimal_separator, $thousand_separator ); // $decimal_places => 0
	    $decimal = sprintf( '%02d', ( $price - intval($price) ) * 100 );
        
        return $unit . $decimal_separator . '<span class="priceSup">' . $decimal . '</span>';   
    }

    public function calculTvaFilter($prixHT, $tva) : float 
    {
        return $prixHT * $tva;
    }

    public function calculMontantTvaFilter($prixHT, $tva) : float 
    {
        return $prixHT / 100 * $tva;
    }

    public function getAmountReduced($prixTTC, $percent) : float 
    {
        $coefficient = 1 - $percent / 100;
        $remise = $prixTTC * $coefficient;
        
        return $remise;
    }

    function createKeywordsFilter($str, $limite) : string
    {
        $ponctuation = array(".", "'", ":",",","\"");
        $str = str_replace($ponctuation,' ', $str);
        $str = explode(' ', $str);

        $keywords = '';

        foreach ($str as $key => $value) 
        {
            if(strlen($value) > $limite)
            {
                $keywords .= "$value, ";
            }
        }

        return substr($keywords, 0, -1);
    }

}
