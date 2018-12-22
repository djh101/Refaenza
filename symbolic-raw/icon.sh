#!/bin/sh

name=${1##*/}
name=${name%-symbolic.svg}
[ -n "$2" ] && size=$2 || size=16
[ "$3" == "light" ] && theme=light || theme=dark

# Create icon
php -c php.ini icon.php ${theme} scalable-up-to-${size}/${name}-symbolic.svg

# Shrink icon using scour
if [ -n "$(command -v scour)" ]; then
	mv ${name}.svg ${name}.old.svg
	scour -i ${name}.old.svg -o ${name}.svg
	rm ${name}.old.svg
fi
