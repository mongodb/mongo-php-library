<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\StreamProcessingClient;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class StreamProcessingClientTest extends TestCase
{
    public function testIsWorkspaceUriDetectsValidEndpoint(): void
    {
        $uri = 'mongodb://atlas-stream-699c842ef433fe6001480b17-etif1.virginia-usa.a.query.mongodb.net/';
        $this->assertTrue(StreamProcessingClient::isWorkspaceUri($uri));
    }

    public function testIsWorkspaceUriDetectsValidEndpointWithCredentialsAndPort(): void
    {
        $uri = 'mongodb://user:pass@atlas-stream-xyz.us-east-1.a.query.mongodb.net:27017/?retryWrites=true';
        $this->assertTrue(StreamProcessingClient::isWorkspaceUri($uri));
    }

    #[DataProvider('provideNonWorkspaceUris')]
    public function testIsWorkspaceUriRejectsOtherEndpoints(string $uri): void
    {
        $this->assertFalse(StreamProcessingClient::isWorkspaceUri($uri));
    }

    public static function provideNonWorkspaceUris(): array
    {
        return [
            'standard cluster' => ['mongodb://localhost:27017/'],
            'srv standard'     => ['mongodb+srv://cluster0.example.mongodb.net/'],
            'similar hostname' => ['mongodb://atlas-stream-x.example.com/'],
            'missing prefix'   => ['mongodb://abc.virginia-usa.a.query.mongodb.net/'],
        ];
    }

    public function testConstructorRejectsNonWorkspaceUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('StreamProcessingClient requires a workspace endpoint URI');
        new StreamProcessingClient('mongodb://localhost:27017/');
    }

    public function testConstructorRejectsSrvScheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StreamProcessingClient('mongodb+srv://atlas-stream-x.us-east-1.a.query.mongodb.net/');
    }

    public function testConstructorRejectsTlsDisabled(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('TLS cannot be disabled');
        // The Manager would also reject if it tried to connect, but we expect
        // our validation to short-circuit before reaching libmongoc.
        new StreamProcessingClient(
            'mongodb://atlas-stream-x.us-east-1.a.query.mongodb.net/',
            ['tls' => false],
        );
    }
}
