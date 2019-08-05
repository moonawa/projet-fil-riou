<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\Exception;
//use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EntrepriseControllerTest extends WebTestCase
{
    public function testShow(){
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Moonawa' ,
                'PHP_AUTH_PW'   => '123456'
        ]);
        $client->request('GET', 'api/entreprise/22');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
    public function testAdd(){
        $client = static::createClient([],[ 
            'PHP_AUTH_USER' => 'Moonawa' ,
            'PHP_AUTH_PW'   => '123456'
    ]);
    $crawler = $client->request('POST', '/api/add/entreprise', [],[],
    ['CONTENT_TYPE'=>"application/json"],
    '{"RaisonSociale":"Moonawa",
        "Ninea":"450856157",
        "Adresse":"Mermoz",
        "Solde":"1000000",
        "Status":"Actif",
        "email":"awandiayesene7@gmail.com",
        "telephone":"779053999"      
    }');
        $rep=$client->getResponse();
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
}

?>