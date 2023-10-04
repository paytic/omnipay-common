<?php

declare(strict_types=1);

namespace Paytic\Omnipay\Common\Tests\Library;

use Paytic\Omnipay\Common\Library\Signer;
use Paytic\Omnipay\Common\Tests\AbstractTest;

/**
 * Class SignerTest
 * @package Paytic\Omnipay\Common\Tests\Library
 */
class SignerTest extends AbstractTest
{
    public function testConvert()
    {
        $key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';

        $signer = new Signer();
        $key = $signer->convertKey($key, Signer::KEY_TYPE_PUBLIC);

        static::assertEquals(
            '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRA
FljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQE
B/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5Ksi
NG9zpgmLCUYuLkxpLQIDAQAB
-----END PUBLIC KEY-----',
            $key
        );
    }

    public function testOpenContentWithRSA()
    {
        $signer = new Signer();
        $signer->setPrivateKey(envVar('MOBILPAY_PRIVATE_KEY'));

        $envKey = base64_decode(
            urldecode(
                "7ZSA6tGKSHJfar2L6YKSog2LY5KMzDMiKPf%2ByE5WU3lJlu0ip0OgcNCZEJ6yU4%2F38M1GmS%2FfFf7ya1O13gkNDQxzrquJcxGT2ZPekWxI6aBz73VPVArUS0KgtQNz9aaNXMUqtF1wKtI8J3JznGkDk4hm39Ut2sovfGRAzU7DiVc%3D"
            )
        );
        $sealedData = base64_decode(
            urldecode(
                "HS9FARzmkh%2FnBHeM2d95Q5rc8GOdITP1SME0mH%2F%2F8ZOfE3QqbaUjCKOQuUBPFEIi7P1Ccf4IEE1Xli30Eb4DTciQyhpFt%2BjJHXjVehUPOLaAJhL7SG34YHy75bsw7idvBWVnxyj9XsmRMwNQVIG4Www9uW0%2Fwth8MSaXPHfc%2BND9uZ%2FCOuHIBJ5FtDgp58ZTXm6TtHo2XDDfVmuKJsSXmoMelwueSyOP8M9cGz%2BDqlSRb1R0Y62Rj7gxkfXVhq630zAxHB6bMmgJ2hIG9eEbubR9KSlLg7WX0RE4fNDaRR1Y7HQdWpaI3I6eVg4K3FqUObc3QlQV7D2qQJczoi2J7qEadWRxyGexduihf2pltZwNyesLjvfCqLwXKYI1xD3rVKBEMqLDKYjvq%2FSQnPgZPZfB8AmZtIAETcTijbN93sDQlUusEUPL9gTCR9JZTwidnYsjRam%2FgR%2BG0%2BFuV5beBq329NBOXRCGRwv89xOPvXzXD3lB6VzwdVGjrT821vPaidx1kaiSifwmDbsdsdTLcqWnYlAaaJwIgz%2Bi6751rIj%2Fp05vCSLeX0UApD53uVOU3bgJPgcUSI6Dj7jt6LLTRw001KxwXSSabHoRaOCVq%2BbdcX5qj9aKpUreXRvIlN4PVbssRW89LsPd%2BgDmsBzU9rSnA7l8hN60unXygSICD3%2BgpOhFrHnNdCIsLY%2Fgvikpp6MgRp5IvqRcWJ%2FpERIHOSmDeWbZmOCg0Jynvs2vwNvY91eS5FK%2FHXHxLVaGmtlVsIjs25UmOfjz6jKmxbWucztE3DpD8s4SPXlrsWg%2FwPo3h5TvF0gWcOvR4D5ZflNoNn0pQAqPkzoY77gxDLGAq1KpsQD2vb%2BVywcEV%2BBBlQbB80LRcimrraM6F%2FdvCnU74v7SyRid%2BJVrtyBsWJWux1vyQ30BIl5X4HQ7rEWQVMK3Nm4HvlwT1BBLLEnusiJOxDcZUZHun9vzbEgeWQ2QiUw3ixccVyEtQDYNbNqx8jzMQFNTaigkrAzPX1m70EFyHTZJzpteQaGZhTwnjBxg7hJaob%2B%2B7jpKdazurl3LNG099TB77Om%2Bzqg5lHj1q6q8EM8RE%2Fb99AT1ai536JLHET5BkUqS9TjQlVJ72asLs6NZnPA1hWQlZXgOMnynqAqupYsYP20tasOY%2BKtehAntTCn2YI875uaINk5B72ZzJANkwE%2BXGmui6q6BWbcDdAnKNadfFaLkf7OJaVvj1Ziok02VXSzX8GYG2rZhdPaRW0IvoPw8ppa%2BJ2NxFyBPzioefGV6OApGODgenZuZTn48HdDoQiZbkv8I40sRptcqJizeQTZCVs1e3LwmlAnM%2Fzhl2PjQbpMqOwVPW6dyX3bTyfWNHMrpJi9UKnotlEC7j9ni2cVOvi3d8mBXSkgjb%2FSYMTNBbiErVeLdA1o3GaM0weAm4BnK7YyssfSEWMxAWo%2BHJUIGKqSWtPiLR6I%2BD1R%2FfyPVgVsTrqeuRZMuvh3gDyZ7difGlahK9FyCYZWdcS1qmoKX1pTox9E3zr1YerevquM7vuK%2Bvuowo4BLADlW6MPRrBk7qedgJyKOAlSML%2B7zezCWDFHL7uTDJqRHpb%2BfOYg1VjFW00yzVU3c5M9zzp%2BYOz0uNrebeA0oxZQn4yDvmTpGmr8OtFuaAbkDEYre6HM7%2Bl%2BZn3mFGIM81d0MHJgP5Cv3669FHgKHme9XGGgeCGBJuAyd61Sjob7vspiVlpOgMPq6ADuYJWStFobscX4LmZjNPfa726ZWLF37aGK7PzPo6f3G3zqsfclI%2B0uMjj2ilTg8UBqE9csMZzsUJCeFcKq2HsOe6eISyELQMDzJBjmQy%2Fb5aDaesqe2UnNH%2BWmSSiJ4SFH40jI4eQK9m0sMA%2Bfp0iIYVUFBLxtaSSj4xsaw0u23Is7Rtuqe57bJmzowFqkJ0jWBD0Li5IuoG%2B595HlU%2B83zSj5tI0pdH2w97row2rE76G%2BfhephaShVKq%2BIRUPVv1dkEL8UX3KgpcoMiV%2BAX%2BoWxf7FKOTc9ES%2B%2BPmiRjrH46%2BVWb7uHA%2F8%2BCkPbXWThkiac1oAdzh5g6vOjoDFvtd773WdwJAnPN16EoqhoATbU%2F6uUp0eV8fJJcLMpmx9SrrzWe4abNDQRRmexRZWuZ5uddk08zHlIrN3sTJi7rk%2BUZ87WSDFhE1Rntf8fH7V15HaPSDC8cSW5RDHP1PHlzHG64mfhHmkvMA%3D"
            )
        );

        $result = $signer->openContentWithRSA($sealedData, $envKey);
        static::assertIsArray($result);
    }

    public function testSealContentWithRSA()
    {
        $content = 'test';
        $signer = new Signer();
        $signer->setCertificate(envVar('MOBILPAY_PUBLIC_CER'));

        $result = $signer->sealContentWithRSA($content);

        static::assertCount(2, $result);
        static::assertIsString($result[0]);
        static::assertIsArray($result[1]);
    }
}
