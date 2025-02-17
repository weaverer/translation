<?php
declare(strict_types=1);

namespace Weaverer\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Weaverer\Translation\Translator;

class TranslatorTest extends TestCase
{
    public function testInstanceIsSingleton()
    {
        $translator1 = Translator::getInstance();
        $translator2 = Translator::getInstance();
        $this->assertSame($translator1, $translator2);
        $translator3 = Translator::getInstance(__DIR__.'/lang');
        $translator4 = Translator::getInstance(__DIR__.'/lang');
        $this->assertSame($translator3, $translator4);
        $this->assertNotSame($translator1, $translator3);
    }

    public function testGetTranslationNull()
    {
        $translator = Translator::getInstance();
        $this->assertEquals('greeting.hello', $translator->get('greeting.hello'));
    }

    public function testGetTranslation()
    {
        $translator = Translator::getInstance();
        $this->assertEquals('These credentials do not match our records.', $translator->get('auth.failed'));
        $this->assertEquals('This password reset token is invalid.', $translator->get('passwords.token'));
        $this->assertEquals('The :attribute field must be accepted when :other is :value.', $translator->get('validation.accepted_if'));
        $this->assertEquals('The email field must be accepted when name is John.', $translator->get('validation.accepted_if',['attribute'=>'email','other'=>'name','value'=>'John']));

    }

    public function testGetTranslationWithReplacement()
    {
        $translator = Translator::getInstance(__DIR__.'/lang');
        $this->assertEquals('This is a test message', $translator->get('test_message.test_message'));
        $this->assertEquals('The age must be between 1 and 150.', $translator->get('test_message.between',['attribute'=>'age','min'=>'1','max'=>'150']));
        $this->assertEquals('这是一个测试消息', $translator->get('test_message.test_message',[],"zh"));
        $this->assertEquals('字段年龄必须在 1 和 150 之间', $translator->get('test_message.between',['attribute'=>'年龄','min'=>'1','max'=>'150'],'zh'));

    }

    public function testChoiceTranslation()
    {
        $translator = Translator::getInstance(__DIR__.'/lang');
        $this->assertEquals('There is one apple', $translator->choice('test_message.apples', 1));
        $this->assertEquals('There are many apples,there are 5', $translator->choice('test_message.apples', 5, ['count'=>5]));
        $this->assertEquals('这里有一个苹果', $translator->choice('test_message.apples', 1,[],"zh"));
        $this->assertEquals('这里有一堆苹果,有 5 个', $translator->choice('test_message.apples', 5, ['count'=>5],"zh"));
    }

    public function testGetAndSetLocale()
    {
        $translator = Translator::getInstance();
        $translator->setLocale('fr');
        $this->assertEquals('fr', $translator->getLocale());
    }

    public function testCloningNotAllowed()
    {
        $this->expectException(\Exception::class);
        $translator = Translator::getInstance();
        $clone = clone $translator;
    }

    public function testSerializationNotAllowed()
    {
        $this->expectException(\Exception::class);
        $translator = Translator::getInstance();
        $serialized = serialize($translator);
        unserialize($serialized);
    }
}
