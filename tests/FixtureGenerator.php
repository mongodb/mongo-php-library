<?php

namespace MongoDB\Tests;

use Faker\Factory;

class FixtureGenerator
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->faker->seed(1234);
    }

    public function createUser()
    {
        return array(
            "username" => $this->faker->unique()->userName,
            "password" => $this->faker->sha256,
            "email" => $this->faker->unique()->safeEmail,
            "firstName" => $this->faker->firstName,
            "lastName" => $this->faker->lastName,
            "phoneNumber" => $this->faker->phoneNumber,
            "altPhoneNumber" => $this->faker->optional(0.1)->phoneNumber,
            "company" => $this->faker->company,
            "bio" => $this->faker->paragraph,
            "createdAt" => $this->faker->dateTimeBetween("2008-01-01T00:00:00+0000", "2014-08-01T00:00:00+0000")->getTimestamp(),
            "addresses" => array(
                $this->createAddress(),
                $this->createAddress(),
                $this->createAddress(),
            ),
        );
    }

    public function createAddress()
    {
        return (object) array(
            "streetAddress" => $this->faker->streetAddress,
            "city" => $this->faker->city,
            "state" => $this->faker->state,
            "postalCode" => $this->faker->postcode,
            "loc" => $this->createGeoJsonPoint(),
        );
    }

    public function createGeoJsonPoint()
    {
        return (object) array(
            "type" => "Point",
            "coordinates" => (object) array(
                $this->faker->longitude,
                $this->faker->latitude
            ),
        );
    }
}
