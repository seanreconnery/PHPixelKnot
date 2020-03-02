<?php

$HeaderA = hex2bin('ffd8ffdb008400');
$HeaderB = hex2bin('ffd8ffe000104a');
$HeaderC = hex2bin('ffd8ffe1001845');

$byteString1 = hex2bin('46494600010100000100010000ffdb');

$byteString2a = hex2bin('0302020302020303030304030304050805050404050a070706080c0a0c0c0b0a0b0b0d0e12100d0e110e0b0b1016101113141515150c0f171816141812141514');
$byteString2b = hex2bin('0403030403030404030405040405060a07060606060d090a080a0f0d10100f0d0f0e11131814111217120e0f151c151719191b1b1b10141d1f1d1a1f181a1b1a');
$byteString2c = hex2bin('49492a00080000000000000000000000ffec00114475636b79000100040000003c0000ffee000e41646f62650064c000000001ffdb0084000604040405040605');


$pkseq = hex2bin('ffc0001108');
$byteString4 = hex2bin('03012200021101031101');

$byteString5a = hex2bin('ffda000c03');
$byteString5b = hex2bin('09e18552cd');
$byteString5c = hex2bin('fee5588e3f');
$byteString5d = hex2bin('8a298c28a2');


$filename = $argv[1];

printf($argv[1]);
print("\n\n");


$f = fopen($filename, "r") or die("Unable to open file!");
$seq = fread($f, 7); // grab the first few bytes of the header


if (strpos($seq, $HeaderA) !== False or strpos($seq, $HeaderB) !== False or strpos($seq, $HeaderC) !== False)
{
    // found the header, so lets read in the whole file, and then check for the various byte strings that are present in PK
    $seq = fread($f, filesize($filename));
    print($filename);
    print("\n");

    if (strpos($seq, $byteString2a) !== False or strpos($seq, $byteString2b) !== False or strpos($seq, $byteString2c) !== False) {
        // found the next set of bytes
        
        if (strpos($seq, $pkseq) !== False) {
            // found PK string
            
            if (strpos($seq, $byteString4) !== False) {
                // found 10 byte string
                
                if (strpos($seq, $byteString5a) !== False or strpos($seq, $byteString5b) !== False or strpos($seq, $byteString5c) !== False or strpos($seq, $byteString5d) !== False) {
                    # found final 5 bytes
                    print("PIXELKNOT indications foumd");
                } else {
                    # not found
                    
                }
            } else {
                # not found
                
            }
        } else {
            # not found
            
        }
    } else {
        # not found
        
    }

    print("No indications of PixelKnot found");

}    

unset($seq);    // clear the string from memory

?>
