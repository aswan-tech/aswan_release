***********************************************
Seller Products by Inchoo
***********************************************
 * @version     1.2.3
 * @category    Inchoo
 * @package     Inchoo Seller Products
 * @authors     Mladen Lotar <mladen.lotar@surgeworks.com>, Vedran Subotic <vedran.subotic@surgeworks.com>
***********************************************


RELEASE NOTES (1.2.3)
***********************************************
 1. Reorganized System->Configuration section - everything from Inchoo is under "Inchoo" tab now

RELEASE NOTES (1.2.1)
***********************************************
 1. Fixed bug when Magento's flat tables are enabled
 2. Admin grid extended with product type column and visibility renderer
 3. Added pagination to seller products view
 4. Price column updated/upgraded with currency in admin grid
 5. Reformatted frontend code + code comments
 6. Removed redundant files
 7. Reorganized template files
 8. Additional field in admin configuration for cms block title
 9. Reorganized template files for community version
10. Added Inchoo's template file for Seller Products listing on separate page, as this way is more stable with your
    modifications on site (if any)
11. Fixed ACL permissions - separated Seller Products Data permission (under Catalog menu) and Seller Products
    Settings permission (under System -> Configuration menu)
12. Added template and layout files for default themes (Community, Professional, Enterprise)
13. Added breadcrumbs for Seller Products page


INSTALLATION
***********************************************
 1. Make a backup of both files and database
 2. Manual: Copy contents of tgz file to your Magento installation root
    Magento Connect: Login to your magentocommerce.com account, copy/paste extension key (Magento Connect v2),
                     enter it to your Magento Connect Manager, and install
 3. If you use custom theme you'll need to copy template and layout files from "app/design/frontend/defailt/default" to
    "app/design/frontend/your_package/your/theme" in order to see anything on frontend
 4. Clear Magento Cache


REMOVAL OF EXTENSION
***********************************************
 1. Make a backup of both files and database
 2. Open "app/etc/modules/Inchoo_SellerProducts.xml" and change "<active>true</active>" to "<active>false</active>"
 3. Test if anything on site got broken (although it shouldn't)
 4. Remove "SellerProducts" directory from "app/code/community/Inchoo" if previous step went well
 5. Remove "Inchoo_SellerProducts.xml" from "app/etc/modules" directory
 6. Clear Magento Cache