<?php

namespace App\Tests\Api;

use App\Api\JsonShopStorage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ShopApiTest extends WebTestCase
{
    public function testApiAndPersistence(): void
    {
        $directory = sys_get_temp_dir().'/shopflow-test-'.bin2hex(random_bytes(8));
        mkdir($directory);
        $products = dirname(__DIR__, 2).'/data/produits.json';
        $state = $directory.'/shopflow.json';
        try {
            $client = static::createClient();
            $client->disableReboot();
            static::getContainer()->set(JsonShopStorage::class, new JsonShopStorage($products, $state));
            $client->request('GET', '/produits');
            self::assertResponseIsSuccessful();
            $list = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            self::assertCount(3, $list);
            self::assertSame(['id', 'name', 'price', 'stock'], array_keys($list[0]));
            $client->request('GET', '/produit/1');
            self::assertResponseIsSuccessful();
            $client->request('GET', '/produit/999');
            self::assertResponseStatusCodeSame(404);
            $client->request('POST', '/commande', server: ['CONTENT_TYPE' => 'application/json'], content: '{"produit_id":1,"quantite":2}');
            self::assertResponseStatusCodeSame(201);
            $order = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
            self::assertSame(200, $order['total']);
            self::assertSame(2, $order['quantite']);
            // Reopen storage to prove persistence rather than just in-memory changes.
            $reopened = new JsonShopStorage($products, $state);
            self::assertSame(8, $reopened->product(1)['stock']);
            self::assertCount(1, json_decode(file_get_contents($state), true)['commandes']);
            foreach (['{', '{}', 'null', '[]', '{"produit_id":1,"quantite":0}', '{"produit_id":1,"quantite":1.5}', '{"produit_id":"1","quantite":1}', '{"produit_id":999,"quantite":1}', '{"produit_id":1,"quantite":999}'] as $invalid) {
                $client->request('POST', '/commande', server: ['CONTENT_TYPE' => 'application/json'], content: $invalid);
                self::assertResponseStatusCodeSame(400, $invalid);
            }
            self::assertSame(8, $reopened->product(1)['stock']);
            self::assertCount(1, json_decode(file_get_contents($state), true)['commandes']);
            $client->request('GET', '/catalogue');
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('[data-product-id="1"][data-product-stock="8"]');
        } finally {
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }
}
