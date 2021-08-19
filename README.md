# Talked client

Call recording for Nextcloud Talk. This app provides a chat based interface to interact with the Talked server component that handles the actual recording.

Check out the Talked server repo for more information on how this works and what the current limitations are. https://github.com/MetaProvide/talked

## Installation and setup

### Register chat command

To use the chat command to interact with the Talked server, the command first needs to be registered with Talk. You can register a command using the occ command. The exact command depends on how you have installed Nextcloud. Below I'll have an example for the official docker container and the snap. For more information about registering a chat command, check out the Talk documenation: https://nextcloud-talk.readthedocs.io/en/latest/commands/

You will find two examples for both the docker container and snap. The only difference is whether you want to allow all registered users in a room to start a recording, or only the moderators.

#### Docker container

##### Only moderators can use
```
php occ talk:command:add recording Talked "php /var/www/html/occ talked:record {ROOM} {ARGUMENTS}" 2 1
```
##### All registered users can use
```
php occ talk:command:add recording Talked "php /var/www/html/occ talked:record {ROOM} {ARGUMENTS}" 2 2
```

#### Snap

##### Only moderators can use
```
occ talk:command:add recording Talked "php /var/www/html/occ talked:record {ROOM} {ARGUMENTS}" 2 1
```
##### All registered users can use
```
occ talk:command:add recording Talked "php /var/www/html/occ talked:record {ROOM} {ARGUMENTS}" 2 2
```

### Configuring Talked

After you have registered the Talk command you should configure Talked. If you head to Admin settings > Talk, then you will find the settings for Talked near the bottom. Here you can specify the URL for your Talked server and whether to use HTTP Basic auth, and the credentials for HTTP Basic auth.

The http / https should be included in the url, for example: https://talked.example.com

## Usage

To use Talked, simply use the `/recording` command in a chat room. If you don't specify any options or run `/recording help` you will get a help message telling you about the different options. The following options are available:

`/recording start` - starts a recording in the current room.  
`/recording stop`  - stops the active recording  
`/recording status` - checks if there is an active recording  
`/recording info` - prints the version number of the Talked server.  
`/recording help` - prints a help message with the different options

## License

This program is licensed under the AGPLv3 or later.
