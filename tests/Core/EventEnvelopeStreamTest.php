<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * For more information, please view the LICENSE file that was distributed with
 * this source code.
 */

namespace F500\EventSourcing\Test\Core;

use F500\EventSourcing\Collection\EventEnvelopeStream;
use F500\EventSourcing\Example\Basket\BasketId;
use F500\EventSourcing\Test\TestHelper;

/**
 * Test EventEnvelopeStream
 *
 * @copyright Copyright (c) 2015 Future500 B.V.
 * @license   https://github.com/f500/event-sourcing/blob/master/LICENSE MIT
 * @author    Jasper N. Brouwer <jasper@nerdsweide.nl>
 */
class EventEnvelopeStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestHelper
     */
    private $testHelper;

    /**
     * @var EventEnvelopeStream
     */
    private $envelopeStream;

    public function setUp()
    {
        $this->testHelper = new TestHelper($this);

        $id = BasketId::fromString('some-id');

        $this->envelopeStream = new EventEnvelopeStream(
            [
                $this->testHelper->getEnvelopeStreamEnvelopeOne($id),
                $this->testHelper->getEnvelopeStreamEnvelopeTwo($id),
                $this->testHelper->getEnvelopeStreamEnvelopeThree($id)
            ]
        );
    }

    public function tearDown()
    {
        $this->testHelper->tearDown();
    }

    /**
     * @test
     * @expectedException \F500\EventSourcing\Exception\InvalidItemInCollection
     */
    public function itContainsOnlyEventEnvelopes()
    {
        new EventEnvelopeStream(
            [new \stdClass()]
        );
    }

    /**
     * @test
     * @expectedException \F500\EventSourcing\Exception\CollectionIsEmpty
     */
    public function itCannotBeEmpty()
    {
        new EventEnvelopeStream(
            []
        );
    }

    /**
     * @test
     */
    public function itExposesWhetherAKeyExistsOrNot()
    {
        $this->assertTrue(isset($this->envelopeStream[0]));
        $this->assertTrue(isset($this->envelopeStream[1]));
        $this->assertTrue(isset($this->envelopeStream[2]));

        $this->assertFalse(isset($this->envelopeStream[3]));
    }

    /**
     * @test
     */
    public function itExposesItemsByKey()
    {
        $id = BasketId::fromString('some-id');

        $envelopeOne   = $this->testHelper->getEnvelopeStreamEnvelopeOne($id);
        $envelopeTwo   = $this->testHelper->getEnvelopeStreamEnvelopeTwo($id);
        $envelopeThree = $this->testHelper->getEnvelopeStreamEnvelopeThree($id);

        $this->assertSame($envelopeOne, $this->envelopeStream[0]);
        $this->assertSame($envelopeTwo, $this->envelopeStream[1]);
        $this->assertSame($envelopeThree, $this->envelopeStream[2]);

        $this->assertNull($this->envelopeStream[3]);
    }

    /**
     * @test
     * @expectedException \F500\EventSourcing\Exception\ObjectIsImmutable
     */
    public function itemsCannotBeReplaced()
    {
        $id       = BasketId::fromString('some-id');
        $envelope = $this->testHelper->getEnvelopeStreamEnvelopeOne($id);

        $this->envelopeStream[0] = $envelope;
    }

    /**
     * @test
     * @expectedException \F500\EventSourcing\Exception\ObjectIsImmutable
     */
    public function itemsCannotBeRemoved()
    {
        unset($this->envelopeStream[0]);
    }

    /**
     * @test
     */
    public function itCanBeCounted()
    {
        $this->assertCount(3, $this->envelopeStream);
    }

    /**
     * @test
     */
    public function itCanBeIteratedOver()
    {
        foreach ($this->envelopeStream as $envelope) {
            $this->assertInstanceOf('F500\EventSourcing\Event\EventEnvelope', $envelope);
        }
    }

    /**
     * @test
     */
    public function itCanBeIterateOverWithIndexes()
    {
        foreach ($this->envelopeStream as $index => $event) {
            $this->assertInternalType('int', $index);
        }
    }
}