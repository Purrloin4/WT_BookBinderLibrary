<?php

namespace App\Tests\Utils;

/**
 * Copied implementation in
 * ApiPlatform\Symfony\Bundle\Test\ApiTestAssertionsTrait.
 */
trait JsonAssertionTrait
{
    use ArraySubsetTrait;

    /**
     * Asserts that the retrieved JSON is equal to $json.
     *
     * Both values are canonicalized before the comparison.
     */
    public function assertJsonEquals(array|string $expected, string $message = ''): void
    {
        if (\is_string($expected)) {
            $expected = json_decode($expected, true, 512, \JSON_THROW_ON_ERROR);
        }
        $actual = static::getActualJson();
        if (!\is_array($expected) || !\is_array($actual)) {
            throw new \InvalidArgumentException('$expectedJson and $actualJson must be array or string (JSON array or JSON object)');
        }

        static::assertEqualsCanonicalizing($expectedJson, $actualJson, $message);
    }

    public function assertJsonContains(array|string $needle): void
    {
        $haystack = static::getActualJson();
        static::assertTrue(static::isArraySubset($haystack, $needle));
    }

    private function decode(string $json): array
    {
        return json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
    }

    private function getActualJson(): array
    {
        $json = $this->client->getResponse()->getContent();

        return $this->decode($json);
    }
}
