<?php
require_once __DIR__ . "/" . "../vendor/autoload.php";

function createUser($faker)
{
    return array(
        "username" => $faker->unique()->userName,
        "password" => $faker->sha256,
        "email" => $faker->unique()->safeEmail,
        "firstName" => $faker->firstName,
        "lastName" => $faker->lastName,
        "phoneNumber" => $faker->phoneNumber,
        "altPhoneNumber" => $faker->optional(0.1)->phoneNumber,
        "company" => $faker->company,
        "bio" => $faker->paragraph,
        "createdAt" => $faker->dateTimeBetween("2008-01-01T00:00:00+0000", "2014-08-01T00:00:00+0000")->getTimestamp(),
        "addresses" => (object)array(
            createAddress($faker),
            createAddress($faker),
            createAddress($faker),
        ),
    );
}

function createAddress($faker)
{
    return (object)array(
        "streetAddress" => $faker->streetAddress,
        "city" => $faker->city,
        "state" => $faker->state,
        "postalCode" => $faker->postcode,
        "loc" => createGeoJsonPoint($faker),
    );
}

function createGeoJsonPoint($faker)
{
    return (object)array(
        "type" => "Point",
        "coordinates" => (object)array($faker->longitude, $faker->latitude),
    );
}


