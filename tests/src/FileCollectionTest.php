<?php

namespace Live\Collection;

use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function objectCanBeConstructed(): FileCollection
    {
        $file = 'test.txt';
        return new FileCollection($file);
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     * @doesNotPerformAssertions
     */
    public function collectionCanBeAdded()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index', 'value', 35);
        $collection->set('index1', 'value');
        $collection->set('index2', 5);
        $collection->set('index3', true);
        $collection->set('index4', 6.5);
        $collection->set('index5', ['data']);
        $collection->set('index6', ['data', 35]);
        $collection->set('index7', ['data', 35, [9.0], 'name' => 'willian']);
    }

    /**
     * @test
     * @depends collectionCanBeAdded
     */
    public function collectionCanBeRetrieved()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index1', 'value');

        $this->assertEquals('value', $collection->get('index1'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function inexistentIndexShouldReturnDefaultValue()
    {
        $collection = new FileCollection('new/test.txt');

        $this->assertNull($collection->get('index1'));
        $this->assertEquals('defaultValue', $collection->get('index1', 'defaultValue'));
    }

    /**
     * @test
     * @depends objectCanBeConstructed
     */
    public function newCollectionShouldNotContainItems()
    {
        $collection = new FileCollection('new/test.txt');
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @depends collectionCanBeAdded
     */
    public function collectionWithItemsShouldReturnValidCount()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index1', 'value');
        $collection->set('index2', 5);
        $collection->set('index3', true);

        $this->assertEquals(8, $collection->count());
    }

    /**
     * @test
     * @depends collectionWithItemsShouldReturnValidCount
     */
    public function collectionCanBeCleaned()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index', 'value');
        $this->assertEquals(8, $collection->count());

        $collection->clean();
        $this->assertEquals(0, $collection->count());
    }

    /**
     * @test
     * @depends collectionCanBeAdded
     */
    public function addedItemShouldExistInCollection()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index', 'value');

        $this->assertTrue($collection->has('index'));
    }

    /**
     * @test
     */
    public function canReadFile()
    {
        $collection = new FileCollection('test-read.txt');
        $collection->set('index', 'value');

        $read = [
            'index' => [
                'text' => 'value',
                'token' => time()+1
            ]
        ];

        $this->assertEquals($read, $collection->read('test-read.txt'));
    }

    /**
     * @test
     */
    public function canWriteToFile()
    {
        $collection = new FileCollection('test-read.txt');

        $write = [
            'index' => [
                'text' => 'value1',
                'token' => time()+1
            ]
        ];

        $collection->write($write);
        $this->assertEquals(null, $collection->read('test-read.txt'));
    }

    /**
     * @test
     * @depends collectionCanBeAdded
     */
    public function checkIfTokenExpires()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index', 'value');

        sleep(2);

        $this->assertNull($collection->get('index'));
    }

    /**
     * @test
     * @depends checkIfTokenExpires
     */
    public function equalTimeCheckIfTokenDoesNotExpire()
    {
        $collection = new FileCollection('test.txt');
        $collection->set('index', 'value', 2);

        sleep(2);

        $this->assertEquals('value', $collection->get('index'));
    }
}
