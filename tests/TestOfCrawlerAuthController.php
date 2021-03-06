<?php
/**
 *
 * ThinkUp/tests/TestOfCrawlerAuthController.php
 *
 * Copyright (c) 2009-2012 Gina Trapani
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkupapp.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2009-2012 Gina Trapani
 */
require_once dirname(__FILE__).'/init.tests.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';
require_once THINKUP_ROOT_PATH.'webapp/config.inc.php';

class TestOfCrawlerAuthController extends ThinkUpUnitTestCase {

    public function testInvalidLogin() {
        //web
        $controller = new CrawlerAuthController(1, array('you@example.com', 'password'));
        $this->assertTrue(isset($controller));
        $results = $controller->go();
        $this->assertPattern('/ERROR: Invalid or missing username and password./', $results);

        //CLI
        $controller = new CrawlerAuthController(2, array('you@example.com', 'password'));
        $this->assertTrue(isset($controller));
        $results = $controller->go();
        $this->assertPattern('/ERROR: Incorrect username and password./', $results);
    }

    public function testSuccessfulLogin() {
        $hashed_pass = ThinkUpTestLoginHelper::hashPasswordUsingCurrentMethod('mypassword', 'test');

        $builder = FixtureBuilder::build('owners', array('id'=>1, 'email'=>'me@example.com', 'pwd'=>$hashed_pass,
        'pwd_salt'=>'test', 'is_activated'=>1, 'is_admin'=>1));

        //CLI
        $controller = new CrawlerAuthController(2, array('me@example.com', 'mypassword'));
        $this->assertTrue(isset($controller));
        $results = $controller->go();
        $this->assertNoPattern('/ERROR: Invalid or missing username and password./', $results);

        //web
        $this->simulateLogin('me@example.com', 1, true);
        $controller = new CrawlerAuthController(1, array('me@example.com', 'mypassword'));
        $this->assertTrue(isset($controller));
        $results = $controller->go();
        $this->assertNoPattern('/ERROR: Invalid or missing username and password./', $results);
    }
}