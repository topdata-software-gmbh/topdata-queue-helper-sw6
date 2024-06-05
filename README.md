# Topdata Queue Helper for Shopware 6

## About

- console commands for debugging sw6 queues
- All commands are in experimental state. Use with caution.
- 04/2024 created

## Installation and Update

### Installation
- clone this repository into your shopware installation's `custom/plugins` directory
- run `bin/console plugin:refresh`
- run `bin/console plugin:install TopdataQueueHelperSW6 -ac`

### Update
- pull the latest changes from the repository
- check if there is update available: `bin/console plugin:refresh`
- then run `bin/console plugin:update TopdataQueueHelperSW6 -c`
- check if update was successful: `bin/console plugin:list`


## Commands

All commands are in experimental state. Use with caution.

      topdata:queue-helper:debug-queue          
      topdata:queue-helper:delete-zombies       
      topdata:queue-helper:enqueue:list         
      topdata:queue-helper:export:list          
      topdata:queue-helper:reset-queue          
      topdata:queue-helper:scheduled-task:list  
                                                
## Changelog

see [Changelog.md](Changelog.md) 