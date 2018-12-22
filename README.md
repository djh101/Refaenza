# Refaenza
This project aims to continue development of the Faenza and Faience icon themes with new icons and other changes to the original themes. Icons are taken primarily from the original Faenza and Faience icon themes and Faience-ng. This package is made up as follows:
- **symbolic-raw**- contains the raw symbolic icons and the PHP script for converting them to light or dark chromatic icons
  - **icon.php**- converts symbolic icons to light or dark chromatic icons
  - **icon.sh**- bash script that calls icon.php with parameters *symbol name*, *size*, and *variant (light/dark)*
- **build**- contains all of the icons and files needed to build the icon theme
  - ***category*.lst**- list of symbolic links from icons in *category* (each space separated item on a line is linked to the first item on the line with the category, if different from the file, indicated with a colon, e.g. *target link1 link2 category3:link3*)
  - **PKGBUILD**- shell script that builds the icon package (for Arch Linux) with symbolic links determined by the .lst files
  - **src**- source directory for the icons
