<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class RouterCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    }

    public function anyWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/any');
        $I->see('any');
    }

    public function notFoundWorks(AcceptanceTester $I)
    {
        $I->sendGet('/');
        // see if response code returned is correct
        $I->seeResponseCodeIs(404);
    }

    public function getWorks(AcceptanceTester $I)
    {
        $I->sendGet('/get');
        // see if response code returned is correct
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('get');

        $I->sendPost('/get');
        // see if response code for invalid http method is 404
        $I->seeResponseCodeIs(404);
    }

    public function postWorks(AcceptanceTester $I)
    {
        $I->sendPost('/post');
        // see if response code returned is correct
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('post');

        $I->sendGet('/post');
        // see if response code for invalid http method is 404
        $I->seeResponseCodeIs(404);
    }

    public function putWorks(AcceptanceTester $I)
    {
        $I->sendPut('/put');
        // see if response code returned is correct
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('put');

        $I->sendGet('/put');
        // see if response code for invalid http method is 404
        $I->seeResponseCodeIs(404);
    }

    public function patchWorks(AcceptanceTester $I)
    {
        $I->sendPatch('/patch');
        // see if response code returned is correct
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('patch');

        $I->sendGet('/patch');
        // see if response code for invalid http method is 404
        $I->seeResponseCodeIs(404);
    }

    public function deleteWorks(AcceptanceTester $I)
    {
        $I->sendDelete('/delete');
        // see if response code returned is correct
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('delete');

        $I->sendGet('/delete');
        // see if response code for invalid http method is 404
        $I->seeResponseCodeIs(404);
    }

    public function paramIntWorks(AcceptanceTester $I)
    {
        $I->sendGet('/int/1');
        // send data of the correct param type
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('1');

        // send data of the incorrect param type
        $I->sendGet('/int/a');
        // see if response code for invalid param type is 404
        $I->seeResponseCodeIs(404);

        // send invalid http method with correct param type
        $I->sendPost('/int/a');
        // see if 404
        $I->seeResponseCodeIs(404);
    }

    public function paramdecimalWorks(AcceptanceTester $I)
    {
        $I->sendGet('/decimal/1.1');
        // send data of the correct param type
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('1.1');

        // send data of the incorrect param type
        $I->sendGet('/decimal/a');
        // see if response code for invalid param type is 404
        $I->seeResponseCodeIs(404);

        // send invalid http method with correct param type
        $I->sendPost('/decimal/1.1');
        // see if 404
        $I->seeResponseCodeIs(404);
    }

    public function paramCharWorks(AcceptanceTester $I)
    {
        $I->sendGet('/char/a');
        // send data of the correct param type
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('a');

        // send data of the incorrect param type
        $I->sendGet('/char/abc');
        // see if response code for invalid param type is 404
        $I->seeResponseCodeIs(404);

        // send invalid http method with correct param type
        $I->sendPost('/char/a');
        // see if 404
        $I->seeResponseCodeIs(404);
    }

    public function paramAnyWorks(AcceptanceTester $I)
    {
        // int
        $I->sendGet('/any/123');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('123');

        // decimal
        $I->sendGet('/any/12.3');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('12.3');

        // string
        $I->sendGet('/string/abc');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('abc');

        // char
        $I->sendGet('/any/a');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('a');

        // send invalid http method
        $I->sendPost('/any/a');
        $I->seeResponseCodeIs(404);
        // see if 404
    }

    public function arrayOfRoutesWorks(AcceptanceTester $I)
    {
        $I->sendGet('/there');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');
        
        $I->sendGet('/are');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');

        $I->sendGet('/many');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');
    }

    public function arrayOfRoutesWithParamsWork(AcceptanceTester $I)
    {
        $I->sendGet('/many/int/5');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('5');
        
        $I->sendGet('/many/10');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('10');

        $I->sendGet('/many/15/complex/url');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('15');
    }

    public function requirementTrueWorks(AcceptanceTester $I)
    {
        $I->sendGet('/true');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('yes');
    }

    public function requirementFalseWorks(AcceptanceTester $I)
    {
        $I->sendGet('/false');
        
        $I->seeResponseCodeIs(401);
    }

    public function routerFindWorks(AcceptanceTester $I)
    {
        $I->sendGet('/name');
        
        $I->seeResponseCodeIs(200);

        $I->see('/name');

        $I->sendGet('/find-complex');

        $I->seeResponseCodeIs(200);

        $I->see('101-1.1-x');

        $I->sendGet('/find-simple');

        $I->seeResponseCodeIs(200);

        $I->see('abc');
    }

    public function testComplexRoute(AcceptanceTester $I) {
        $I->sendGet('/test/101/complex/1.1/x/test');

        $I->seeResponseCodeIs(200);

        $I->see('101-1.1-x');
    }

    public function redirectWorks(AcceptanceTester $I)
    {
        // test http code
        $I->stopFollowingRedirects();

        $I->sendGet('/redirect');
        
        $I->seeResponseCodeIs(302);

        // see if redirect

        $I->startFollowingRedirects();

        $I->sendGet('/redirect');
        
        $I->seeResponseCodeIs(200);

        $I->see('/name');
    }

    public function permanentRedirectWorks(AcceptanceTester $I)
    {
        // test http code
        $I->stopFollowingRedirects();

        $I->sendGet('/permanent-redirect');
        
        $I->seeResponseCodeIs(301);
    }

    public function prefixWorks(AcceptanceTester $I)
    {
        $I->sendGet('/prefix/any');

        $I->seeResponseCodeIs(200);

        $I->see('any');
    }

    public function prefixWithIndividualTrueRequirementWorksWorks(AcceptanceTester $I)
    {
        $I->sendGet('/prefix/true');

        $I->seeResponseCodeIs(200);

        $I->see('true');
    }

    public function prefixWithIndividualFalseRequirementWorksWorks(AcceptanceTester $I)
    {
        $I->sendGet('/prefix/false');

        $I->seeResponseCodeIs(401);
    }

    public function groupsWithTrueRequirementWorks(AcceptanceTester $I)
    {
        // send get
        $I->sendGet('/true/get');
        
        $I->seeResponseCodeIs(200);

        $I->see('get');

        // send post
        $I->sendPost('/true/post');
        
        $I->seeResponseCodeIs(200);

        $I->see('post');

        // int
        $I->sendGet('/true/any/123');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('123');

        // decimal
        $I->sendGet('/true/any/12.3');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('12.3');

        // string
        $I->sendGet('/true/string/abc');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('abc');

        // char
        $I->sendGet('/true/any/a');
        // send data
        $I->seeResponseCodeIs(200);
        // see if data returned is correct
        $I->see('a');

        // send invalid http method
        $I->sendPost('/true/get/a');
        $I->seeResponseCodeIs(404);
        // see if 404

        // test array of routes

        $I->sendGet('/true/there');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');
        
        $I->sendGet('/true/are');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');

        $I->sendGet('/true/many');
        
        $I->seeResponseCodeIs(200);
        
        $I->see('many');

        // test http code
        $I->stopFollowingRedirects();

        $I->sendGet('/true/redirect');
        
        $I->seeResponseCodeIs(302);

        // see if redirect

        $I->startFollowingRedirects();

        $I->sendGet('/true/redirect');
        
        $I->seeResponseCodeIs(200);

        $I->see('/name');
    }

    public function groupsWithFalseRequirementWorks(AcceptanceTester $I)
    {
        // send get
        $I->sendGet('/false/get');
        
        $I->seeResponseCodeIs(401);

        // send post
        $I->sendPost('/false/post');
        
        $I->seeResponseCodeIs(401);

        // int
        $I->sendGet('/false/any/123');
        // send data
        $I->seeResponseCodeIs(401);

        // decimal
        $I->sendGet('/false/any/12.3');
        // send data
        $I->seeResponseCodeIs(401);

        // string
        $I->sendGet('/false/string/abc');
        // send data
        $I->seeResponseCodeIs(401);

        // char
        $I->sendGet('/false/any/a');
        // send data
        $I->seeResponseCodeIs(401);

        // send invalid http method
        $I->sendPost('/false/get/a');
        $I->seeResponseCodeIs(404);
        // see if 404

        // test array of routes

        $I->sendGet('/false/there');
        
        $I->seeResponseCodeIs(401);
        
        $I->sendGet('/false/are');
        
        $I->seeResponseCodeIs(401);

        $I->sendGet('/false/many');
        
        $I->seeResponseCodeIs(401);

        // test http code
        $I->stopFollowingRedirects();

        $I->sendGet('/false/redirect');
        
        $I->seeResponseCodeIs(302);

        // see if redirect

        $I->startFollowingRedirects();

        $I->sendGet('/false/redirect');
        
        $I->seeResponseCodeIs(200);

        $I->see('/name');
    }

    public function controllersWork(AcceptanceTester $I)
    {
        // send get
        $I->sendGet('/controller');
        $I->seeResponseCodeIs(200);
        $I->see('web');

        $I->sendGet('/controller/abc');
        $I->seeResponseCodeIs(200);
        $I->see('abc');
    }

    public function charComplexWorks(AcceptanceTester $I) {
        $I->sendGet('/char/a/char/b/1/char');
        $I->seeResponseCodeIs(200);
        $I->see('a-b-1');

        $I->sendGet('/char/aa/char/bb/11/char');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/char/a/char/b/1/char/test');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/char/complex');
        $I->seeResponseCodeIs(200);
        $I->see('a-b-1');
    }

    public function intComplexWorks(AcceptanceTester $I) {
        $I->sendGet('/int/1/int/22/333/int');
        $I->seeResponseCodeIs(200);
        $I->see('1-22-33');

        $I->sendGet('/int/a/int/b/c/int');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/int/1/int/22/333/int/test');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/int/complex');
        $I->seeResponseCodeIs(200);
        $I->see('1-22-33');
    }

    public function stringComplexWorks(AcceptanceTester $I) {
        $I->sendGet('/string/a/string/b/c/string');
        $I->seeResponseCodeIs(200);
        $I->see('a-b-c');

        $I->sendGet('/string/a/string/b/c/string/test');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/string/complex');
        $I->seeResponseCodeIs(200);
        $I->see('a-b-c');
    }

    public function decimalComplexWorks(AcceptanceTester $I) {
        $I->sendGet('/decimal/1.1/decimal/2.22/3.333/decimal');
        $I->seeResponseCodeIs(200);
        $I->see('1.1-2.22-3.333');

        $I->sendGet('/decimal/a/decimal/b/c/decimal');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/decimal/1.1/decimal/2.22/3.333/decimal/test');
        $I->seeResponseCodeIs(404);

        $I->sendGet('/decimal/complex');
        $I->seeResponseCodeIs(200);
        $I->see('1.1-2.22-3.333');
    }

    public function jsonWorks(AcceptanceTester $I) {
        $I->sendGet('/json');
        $I->seeResponseCodeIs(200);
        $I->see('{"data":{"nested":{"array":{"of":{"data":"here"}}}}}');
        
        $I->sendGet('/json');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['data' => ['nested' => ['array' => ['of' => ['data' => 'here']]]]]);
    }

    public function jsonParamWorks(AcceptanceTester $I) {
        $I->sendGet('/json/string/1/1.2/a');
        $I->seeResponseCodeIs(200);
        $I->see('{"string":"string","int":"1","decimal":"1.2","char":"a"}');
        
        $I->sendGet('/json/string/1/1.2/a');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['string' => 'string', 'int' => 1, 'decimal' => 1.2, 'char' => 'a']);
    }

    public function falseRequirementReturnsRouter(AcceptanceTester $I) {
        $I->sendGet('/false/get');
        $I->seeResponseCodeIs(401);
        $I->see('{"route":"\/false\/get"}');
        $I->seeResponseContainsJson(['route' => '/false/get']);
    }
}
