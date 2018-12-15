# TCMP DELETE ORDERS

# Description

Enables a  mass action for order deletion on  the Sales Order Grid and a delete button on the sales order view.  Both of these actions can be restricted using Magento's ACL.

#Installation
- Extract the archive into your Magento root
- Run the following commands:
    - php bin/magento setup:upgrade
    - php bin/magento setup:static-content:deploy
    - php bin/magento cache:flush