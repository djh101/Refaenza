<?php

define('CURDIR', __DIR__);

if ($_SERVER['argv'][1] == 'light') {
  $type = 'light';
  $bkg = '/96light.svg';
} elseif ($_SERVER['argv'][1] == 'dark') {
  $type = 'dark';
  $bkg = '/96dark.svg';
}

if (!file_exists($_SERVER['argv'][2])) {
    echo "File " . $_SERVER['argv'][2] . " does not exist.\n";
    exit;
}

# Copy original image to outfile
$file = CURDIR . '/' . str_replace('-symbolic.svg ', '.svg', basename($_SERVER['argv'][2]) . " ");
copy($_SERVER['argv'][2], $file);

$xml = simplexml_load_file($file);
$w = (int)$xml['width'];
$h = (int)$xml['height'];
$big = ($w === 96);

# Output background if big
if ($big) {
    $output = file_get_contents(CURDIR . $bkg);
    $output = str_replace('</svg>', '', $output);
} else {
    $output = '<svg width="' . $w . '" height="' . $h . '" version="1.1" xmlns="http://www.w3.org/2000/svg">' . "\n";
}

# Simple icon, no highlighting
if (count($xml->path) === 1 && isset($xml->path['opacity'])) {
    if ($type === 'light') {
        $xml->path['fill'] = '#000';
        $xml->path['opacity'] = ($big ? '0.3' : '0.2');
    } elseif ($type === 'dark') {
        $xml->path['fill'] = '#fff';
        $xml->path['opacity'] = ($big ? '0.2' : '0.4');
    }
    $output .= $xml->path->asXML();
    $output .= '</svg>';
    file_put_contents($file, $output);
} else {
    # Small icon
    if (!$big) {
        $output = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
   xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
   height="' . $h . '"
   width="' . $w . '"
   inkscape:version="0.92.2 2405546, 2018-03-11">';
    }
    $output .= (!$big || $type === 'dark') ? '<defs>' : '<defs>
		<filter id="w">
			<feGaussianBlur stdDeviation="0.68880677"/>
		</filter>';

for ($i=0; $i < count($xml->path); ++$i) {
    if ((string)$xml->path[$i]['fill'] !== '#bebebe' || !isset($xml->path[$i]['opacity'])) {
        $sizes = explode(',', shell_exec("inkscape -S '$file' | head -" . ($i+2) . " | tail -1"));
        $sizes[2] = floatval($sizes[2]);
        $sizes[4] = floatval($sizes[4]);
        
        ###if ($type === 'light' && (string)$xml->path[$i]['fill'] === '#bebebe') {
        if ($type === 'light' && ((string)$xml->path[$i]['fill'] === '#bebebe' || $i === 0)) {
            $l = round($sizes[2]) + 1;
            $k = 10 - $l - 1;
            if ($xml['height'] <= 16) {
                $r = 11;
                $cx = 9;
                $cy = 10;
            } else {
                $r = 16;
                $cx = 12;
                $cy = 13;
                $l = round($sizes[2]) + 1;
                $k = 11 - $l;
            }
            $output .= "\n" . '<radialGradient id="gr' . $i . '" gradientUnits="userSpaceOnUse" cy="' . $cy . '" cx="' . $cx . '" gradientTransform="matrix(1 0 0 .' . $k . $l . $k . $l . $k . ' 0 ' . $l . '.' . $k . $l . $k . ($l+1) . ')" r="' . $r . '">' . "\n";
            if ((string) $xml->path[$i]['fill'] === '#d40000') {
                $output .= '
                <stop stop-color="#e64b36" offset="0"/>
                <stop stop-color="#a31414" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#008000') {
                $output .= '
                <stop stop-color="#b0e929" offset="0"/>
                <stop stop-color="#7ea424" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#ff9000') {
                $output .= '
                <stop stop-color="#df880b" offset="0"/>
                <stop stop-color="#f7c15a" offset="1"/>
                ';
            # Battry 60 and 80
            } elseif ((string) $xml->path[$i]['fill'] === '#e6d200') {
                $output .= '
                <stop stop-color="#ffef40" offset="0"/>
                <stop stop-color="#d9b60b" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#77b300') {
                $output .= '
                <stop stop-color="#e1eb23" offset="0"/>
                <stop stop-color="#a7b32d" offset="1"/>
                ';
            } else {
                ###
                if ($type === 'dark') {
                    $output .= '
                    <stop stop-color="' . ($type === 'light' ? '#1e1e1e' : '#ebebeb') . '" offset="0"/>
                    <stop stop-color="' . ($type === 'light' ? '#505050' : '#aaa') . '" offset="1"/>
                    ';
                } else {
                    $output .= '
                    <stop stop-opacity="0.23529" offset="0"/>
                    <stop stop-opacity="0.54902" offset="1"/>
                    ';
                }
            }
            $output .= "\n" . '</radialGradient>' . "\n";
        } else {
            $output .= "\n" . '<linearGradient id="gr' . $i . '" gradientUnits="userSpaceOnUse" x1="' . ($w/2) . '" y1="' . $sizes[2] . '" x2="' . ($w/2) . '" y2="' . ($sizes[2] + $sizes[4]) . '">' . "\n";
            if ((string) $xml->path[$i]['fill'] === '#d40000') {
                $output .= '
                <stop stop-color="' . ($type === 'light' ? '#c80000' : '#f3604d') . '" offset="0"/>
                <stop stop-color="' . ($type === 'light' ? '#f3604d' : '#c81700') . '" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#008000') {
                $output .= '
                <stop stop-color="' . ($type === 'light' ? '#0aad09' : '#31cb38') . '" offset="0"/>
                <stop stop-color="' . ($type === 'light' ? '#31cb38' : '#0aad09') . '" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#ff9000') {
                $output .= '
                <stop stop-color="' . ($type === 'light' ? '#df880b' : '#f7c15a') . '" offset="0"/>
                <stop stop-color="' . ($type === 'light' ? '#f7c15a' : '#df880b') . '" offset="1"/>
                ';
            # Battry 60 and 80
            } elseif ((string) $xml->path[$i]['fill'] === '#e6d200') {
                $output .= '
                <stop stop-color="' . ($type === 'light' ? '#bfa313' : '#f2e449') . '" offset="0"/>
                <stop stop-color="' . ($type === 'light' ? '#f2e449' : '#bfa313') . '" offset="1"/>
                ';
            } elseif ((string) $xml->path[$i]['fill'] === '#77b300') {
                $output .= '
                <stop stop-color="' . ($type === 'light' ? '#86bf13' : '#9ce60b') . '" offset="0"/>
                <stop stop-color="' . ($type === 'light' ? '#9ce60b' : '#86bf13') . '" offset="1"/>
                ';
            } else {
                if ($type === 'dark') {
                    $output .= '
                    <stop stop-color="' . ($type === 'light' ? '#1e1e1e' : '#ebebeb') . '" offset="0"/>
                    <stop stop-color="' . ($type === 'light' ? '#505050' : '#aaa') . '" offset="1"/>
                    ';
                } else {
                    $output .= '
                    <stop stop-opacity="0.23529" offset="0"/>
                    <stop stop-opacity="0.54902" offset="1"/>
                    ';
                }
            }
            $output .= "\n" . '</linearGradient>' . "\n";
        }
    }
}
$output .= '</defs>';

if ($big && $type === 'light') {
    $output .= '<g transform="translate(0,-4)">';
    ###$output .= '<g transform="translate(0,-2)">';
}

# Bevels
#if ((string)$xml->path[$i]['fill'] !== '#bebebe' || !isset($xml->path[$i]['opacity'])) {
#if (((string)$xml->path[$i]['fill'] === '#bebebe' || $i === 0) && !isset($xml->path[$i]['opacity'])) {
$output .= '<g id="Bevel1">';
$output .= '<g id="Bevel1outer">';
for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        $path = clone($xml->path[$i]);
        $path['fill'] = ($big || $type === 'dark' ? '#000' : '#fff');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.5' : '0.05');
        } else if ($type === 'dark') {
            $path['opacity'] = ($big ? '0.15' : '0.25');
        }
        if ($big && $type === 'light') {
            $path['filter'] = 'url(#w)';
            $output .= $path->asXML();
            $path = clone($xml->path[$i]);
            $path['fill'] = '#000';
            $path['opacity'] = '0.5';
        }
        if ($big) {
            $path['transform'] = 'translate(0,1)';
        }
        $path['inkscape:original'] = $path['d'];
        $path['sodipodi:type'] = 'inkscape:offset';
        $path['inkscape:radius'] = ($big && $type === 'light' ? '-1' : '1');

        unset($path['d']);
        $output .= $path->asXML();
    }
}
$output .= "</g>\n";
$output .= '<g id="Bevel1inner">';
for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        ###if (!$big) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = ($type === 'light' ? '0.05' : '0.25');
            $path['fill'] = ($type === 'light' ? '#fff' : '#000');
            $output .= $path->asXML();
        ###}
    }
}
$output .= "</g>\n";
/*for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        $output .= '<g id="Bevel1-'.$i.'">';
        $path = clone($xml->path[$i]);
        $path['fill'] = ($big || $type === 'dark' ? '#000' : '#fff');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.5' : '0.05');
        } else if ($type === 'dark') {
            $path['opacity'] = ($big ? '0.15' : '0.25');
        }
        if ($big && $type === 'light') {
            $path['filter'] = 'url(#w)';
            $output .= $path->asXML();
            $path = clone($xml->path[$i]);
            $path['fill'] = '#000';
            $path['opacity'] = '0.5';
        }
        if ($big) {
            $path['transform'] = 'translate(0,1)';
        }
        $path['inkscape:original'] = $path['d'];
        $path['sodipodi:type'] = 'inkscape:offset';
        $path['inkscape:radius'] = ($big && $type === 'light' ? '-1' : '1');

        unset($path['d']);
        $output .= $path->asXML();

        if (!$big) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = ($type === 'light' ? '0.05' : '0.25');
            $path['fill'] = ($type === 'light' ? '#fff' : '#000');
            $output .= $path->asXML();
        }
        $output .= "</g>\n";
    }
}*/
$output .= "</g>\n";

if ($big && $type === 'dark') {
    $output .= '<g id="Bevel2">';
    $output .= '<g id="Bevel2outer">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = '0.1';
            $path['fill'] = '#000';
            $path['inkscape:radius'] = '2';
            $path['inkscape:original'] = $path['d'];
            $path['sodipodi:type'] = 'inkscape:offset';
            $path['transform'] = 'translate(0,1)';
            unset($path['d']);
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= '<g id="Bevel2inner">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = ($type === 'light' ? '0.05' : '0.25');
            $path['fill'] = ($type === 'light' ? '#fff' : '#000');
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= "</g>\n";

    $output .= '<g id="Bevel3">';
    $output .= '<g id="Bevel3outer">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = '0.08';
            $path['fill'] = '#000';
            $path['inkscape:radius'] = '3';
            $path['inkscape:original'] = $path['d'];
            $path['sodipodi:type'] = 'inkscape:offset';
            $path['transform'] = 'translate(0,1)';
            unset($path['d']);
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= '<g id="Bevel3inner">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['opacity'] = ($type === 'light' ? '0.05' : '0.25');
            $path['fill'] = ($type === 'light' ? '#fff' : '#000');
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= "</g>\n";
    /*for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $output .= '<g id="Bevel2-'.$i.'">';
            $path = clone($xml->path[$i]);
            $path['opacity'] = '0.1';
            $path['fill'] = '#000';
            $path['inkscape:radius'] = '2';
            $path['inkscape:original'] = $path['d'];
            $path['sodipodi:type'] = 'inkscape:offset';
            $path['transform'] = 'translate(0,1)';
            unset($path['d']);
            $output .= $path->asXML();
            $output .= "</g>\n";

            $output .= '<g id="Bevel3-'.$i.'">';
            $path = clone($xml->path[$i]);
            $path['opacity'] = '0.08';
            $path['fill'] = '#000';
            $path['inkscape:radius'] = '3';
            $path['inkscape:original'] = $path['d'];
            $path['sodipodi:type'] = 'inkscape:offset';
            $path['transform'] = 'translate(0,1)';
            unset($path['d']);
            $output .= $path->asXML();
            $output .= "</g>\n";
        }
    }
    $output .= "</g>\n";*/
}

# Main Image
for ($i=0; $i < count($xml->path); ++$i) {
	if ((string)$xml->path[$i]['fill'] === '#bebebe' && isset($xml->path[$i]['opacity'])) {
		$path = clone($xml->path[$i]);
		$path['id'] = 'base';
		$path['fill'] = ($type === 'light' ? '#000' : '#fff');
		$path['opacity'] = ($type === 'light' ? '0.2' : ($big ? '0.2' : '0.4'));
		$output .= $path->asXML();
		$output .= "\n";
   } else {
        $path = clone($xml->path[$i]);
        $path['id'] = 'base';
        $path['fill'] = 'url(#gr' . $i . ')';
        if ($type === 'light' && $big) {
            $path['opacity'] = isset($path['opacity']) ? '0.3' : '0.8';
        }
        $output .= $path->asXML();
        $output .= "\n";
    }
}
# Highlight and Shadow
if ($type === 'light' && !$big) {
    $output .= '<g id="Bevel2">';
    $output .= '<g id="Bevel2outer">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['fill'] = '#000';
            $path['opacity'] = '0.3';
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= '<g id="Bevel2inner">';
    for ($i=0; $i < count($xml->path); ++$i) {
        if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
            $path = clone($xml->path[$i]);
            $path['fill'] = '#000';
            $path['opacity'] = '0.3';
            $path['inkscape:original'] = $path['d'];
            $path['sodipodi:type'] = 'inkscape:offset';
            $path['inkscape:radius'] = '-0.5';
            unset($path['d']);
            $output .= $path->asXML();
        }
    }
    $output .= "</g>\n";
    $output .= "</g>\n";
}

$output .= '<g id="BevelShadow">';
$output .= '<g id="BevelShadowOuter">';
for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        $path = clone($xml->path[$i]);
        $path['fill'] = ($type === 'light' ? '#fff' : '#000');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.6' : '0.2');
        } else if ($type === 'dark') {
            $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.55' : '0.45');
        }
        $path['transform'] = 'translate(0,1)';
        $output .= $path->asXML();
    }
}
$output .= "</g>\n";
$output .= '<g id="BevelShadowInner">';
for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        $path = clone($xml->path[$i]);
        $path['fill'] = ($type === 'light' ? '#fff' : '#000');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.6' : '0.2');
        } else if ($type === 'dark') {
            $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.55' : '0.45');
        }
        $output .= $path->asXML();
    }
}
$output .= "</g>\n";
$output .= "</g>\n";
for ($i=0; $i < count($xml->path); ++$i) {
    if (!isset($xml->path[$i]['opacity']) && strpos($xml->path[$i]['class'], 'fill') === false) {
        /*$output .= '<g id="BevelShadow-'.$i.'">';
        $path = clone($xml->path[$i]);
        $path['fill'] = ($type === 'light' ? '#fff' : '#000');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.6' : '0.2');
        } else if ($type === 'dark') {
            $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.55' : '0.45');
        }
        $path['transform'] = 'translate(0,1)';
        $output .= $path->asXML();

        $path = clone($xml->path[$i]);
        $path['fill'] = ($type === 'light' ? '#fff' : '#000');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.6' : '0.2');
        } else if ($type === 'dark') {
            $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.55' : '0.45');
        }
        $output .= $path->asXML();
        $output .= "</g>\n";*/

        $output .= '<g id="BevelHighlight-'.$i.'">';
        $path = clone($xml->path[$i]);
        $path['fill'] = ($type === 'light' ? '#000' : '#fff');
        if ($type === 'light') {
            $path['opacity'] = ($big ? '0.4' : '0.3');
        } else if ($type === 'dark') {
            $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.3' : '0.6');
        }
        $output .= $path->asXML();

        if ($type === 'dark' || !$big) {
            $path = clone($xml->path[$i]);
            $path['fill'] = ($type === 'light' ? '#000' : '#fff');
            if ($type === 'light') {
                $path['opacity'] = '0.3';
            } else if ($type === 'dark') {
                $path['opacity'] = ((string) $xml->path[$i]['fill'] !== '#bebebe' ? '0.3' : '0.6');
            }
            $path['transform'] = 'translate(0,1)';
            $output .= $path->asXML();
        }
        $output .= "</g>\n";
    }
}
	
$output .= '</svg>';

file_put_contents($file, $output);

# Exit if third parameter is set
if (!empty($_SERVER['argv'][4])) {
	exit();
}

# Edit in Inkscape
$shellcmd = "inkscape --file='$file'";
for ($i=0; $i < count($xml->path); ++$i) {
    if ((string)$xml->path[$i]['fill'] !== '#bebebe' || !isset($xml->path[$i]['opacity'])) {
        $shellcmd .= " --select=BevelHighlight-".$i." --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
        #$shellcmd .= " --select=Bevel1-".$i." --verb=SelectionUnGroup --verb=SelectionDiff";
        if (!$big || $type === 'light') {
            $shellcmd .= " --verb=EditDeselect";
        }
        /*if ($big && $type === 'dark') {
            $shellcmd .= " --select=Bevel2-".$i." --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect \
 --select=Bevel3-$i --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
        } else if (!$big && $type === 'light') {
            $shellcmd .= " --select=Bevel2-".$i." --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
        }*/
        /*if ($big && $type === 'light') {
            $shellcmd .= " --select=BevelShadow-".$i." --verb=SelectionUnGroup --verb=ObjectSetClipPath --verb=EditDeselect";
        } else {
            $shellcmd .= " --select=BevelShadow-".$i." --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
        }*/
    }
}
$shellcmd .= " --select=Bevel1outer --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
$shellcmd .= " --select=Bevel1inner --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
$shellcmd .= " --select=Bevel1 --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
if (($big && $type === 'dark') || (!$big && $type === 'light')) {
    $shellcmd .= " --select=Bevel2outer --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
    $shellcmd .= " --select=Bevel2inner --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
    $shellcmd .= " --select=Bevel2 --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
}
if ($big && $type === 'dark') {
    $shellcmd .= " --select=Bevel3outer --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
    $shellcmd .= " --select=Bevel3inner --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
    $shellcmd .= " --select=Bevel3 --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
}
$shellcmd .= " --select=BevelShadowOuter --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
$shellcmd .= " --select=BevelShadowInner --verb=SelectionUnGroup --verb=SelectionUnion --verb=EditDeselect";
$shellcmd .= " --select=BevelShadow --verb=SelectionUnGroup --verb=SelectionDiff --verb=EditDeselect";
$shellcmd .= " --verb=FileSave --verb=FileQuit";
shell_exec($shellcmd);
shell_exec("inkscape --file='$file' --export-plain-svg='$file'");

}

if (isset($_SERVER['argv'][3])) {
  copy($file, $_SERVER['argv'][3]); // . '/' . basename($file)
  unlink($file);
}
