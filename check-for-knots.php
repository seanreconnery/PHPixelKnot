<?php 

$HeaderA = hex2bin('ffd8ffdb008400');
$HeaderB = hex2bin('ffd8ffe000104a');
$byteString1 = hex2bin('46494600010100000100010000ffdb00');
$byteString2 = hex2bin('0302020302020303030304030304050805050404050a070706080c0a0c0c0b0a0b0b0d0e12100d0e110e0b0b1016101113141515150c0f171816141812141514');
$pkseq = hex2bin('ffc0001108');
$byteString4 = hex2bin('03012200021101031101');
$byteString5 = hex2bin('ffda000c03');

$filename = $argv[1];

printf($argv[1]);
print("\n\n");

$crossing = 1 - strlen($pkseq); //  set a negative value to backtrack thru byte chunks if necessary
$buflen = 128;

$f = fopen($filename, "r") or die("Unable to open file!");
$seq = fread($f, 7); // grab the first few bytes of the header

if(strpos($seq, $HeaderA) !== False or strpos($seq, $HeaderB) !== False) 
{
    // found the jpeg header, parse thru the rest of the file
    // look for the next set of bytes
    if (strpos($seq, $HeaderA) !== False) {$split = False;}  // if the rest of the strings are found, this is will be ORIGINAL PIXEL KNOT header
    if (strpos($seq, $HeaderB) !== False) {$split = True;}   // if the rest of the strings are found, this will be MODIFIED PIXEL KNOT header
    
    if ($split == True) {
        $seq .= fread($f, 18);
        // look for 18 byte string -- this could indicate a potential modified PixelKnot header
        // If Split is NOT true, but we still find all the other strings, then it means a probable (original) PixelKnot header

        if (strpos($seq, $byteString1) !== False) {
            // found the 18 byte string, go onto the next part
        } else {
            // didn't find the 18 bytes, this probably isn't a stego'ed image file (at least not with PixelKnot)
            exit;
        }
    }
        
    $seq .= fread($f, $buflen);
    if(strpos($seq, $byteString2)) 
    {
        // still finding the correct byte strings, so lets keep going
        $seq .= fread($f, $buflen);
        if(strpos($seq, $pkseq)) 
        {
            // on to the next one.. 
            $seq .= fread($f, 32);
            if(strpos($seq, $byteString4)) 
            { 
                    //final 5 byte string
                    while(!feof($f)) 
                        {
                        $seq .= fread($f, 10);
                            if(strpos($seq, $byteString5) === false) // strict compare (although you've already checked the 0 pos byte for jpg header)
                            // since this can potentially return 0, best practice I guess is use strict compare.
                            {
                                // keep last n-1 bytes, because they can be beginning of required sequence
                                $seq = substr($seq, $crossing);
                            }
                            else
                            {
                                // PIXEL KNOT FILE -- modified or original headers though?
                                if ($split) {
                                    echo $argv[1] . " -- (modified) PixelKnot header detected";
                                } else {
                                    echo $argv[1] . " -- PixelKnot header detected";
                                }                                    
                                    break;
                                }
                            }
                        }

            } 
        
    }

}
unset($seq);    // clear the string from memory

?>
