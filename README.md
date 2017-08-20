# github-notification-processor


Do you follow different repositories which are updated very often on GitHub leaving you with a lot of unread notifications ?

Maybe not all those notifications are interesting for you and you would like to save some time by marking them as read or even unsubscribe from those thread automatically.

Now you can by schedule a cron to do this automatically for you:

```bash
php bin/console github:notifications:process
```

## Requirements

The following packages are required to be installed on your system:
- git
- php 7
- [composer](https://getcomposer.org/download/)
- make

## Installation

After cloning the repository you should be able to launch the command below:
```bash
make install
```

## Settings
Rename provided __app/config.yml.dist__ file to __app/config.yml__ and edit it according to your preference.

#### GitHub Authentication
Make sure you have filled in the correct [GitHub authentication settings](https://developer.github.com/v3/#authentication) for:
- Basic authentication (username/password)
- OAuth
- OAuth2

#### Repositories, filters & actions taken
Edit the repositories, filters and actions according to the specifications from the YAML sample file provided. 

## Usage

The see a list of all commands simply run:

```bash
php bin/console
```

Main commands are listed below with a short description associated to them:

#### List all notifications

```bash
php bin/console github:notifications:list
```

A sample output would like below:
    

| Id        | Repository     | Type | Title                                                       | Url                                                                    |
| --------- | -------------- | ---- | ------------------------------------------------------------|----------------------------------------------------------------------- |
| 250470312 | home-assistant | PR   | Fix Geizhals index issue when not 4 prices available        | https://api.github.com/repos/home-assistant/home-assistant/pulls/9035  |
| 250521011 | home-assistant | I    | MQTT input for sensors/automations not working after update | https://api.github.com/repos/home-assistant/home-assistant/issues/9036 |
| 249815264 | home-assistant | I    | View for un-grouped items when using default view           | https://api.github.com/repos/home-assistant/home-assistant/issues/9009 |
There are 3 notifications

#### Process all notifications

```bash
php bin/console github:notifications:process
```

Based on your __app/config.yml__ file, a sample output would like below:
```bash
Unsubscribe from #250470312 - Fix Geizhals index issue when not 4 prices available
Unsubscribe from #250352152 - Update Fitbit sensor (variable battery icons and formatted names/values)
There were 2/3 notifications unsubscribed
```
