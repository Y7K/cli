# Y7K CLI

## Installation

1. Clone this repository: `git clone <repo-url> <directory>`

2. Change into the  directory and run `composer install && composer run-script project-created-cmd`

3. Place the directory path in your PATH, e.g. by adding
    ```
    export PATH=$PATH:$HOME/path/to/cli
    ```
    to your `.bash_profile` to `.zshrc` file.
    
    This will allow you to run the `y7k` command globally.

4. Create a [Personal GitHub Access Token](https://github.com/settings/tokens) and add it along with your GitHub Username to the `.env` file.

## Update

To update the CLI, pull the latest version:
```
git pull
```

## Commands

### y7k install

Installs a new Project.

```
y7k install
y7k i
```

Launches the installation process. You will be prompted for a name and to configure the project stack.
You can specify the directory with the second argument and a platform with the `--platform` option:

```
y7k install pr01-project
y7k install pr01-project --platform craft
y7k install pr01-project --platform laravel
```


### y7k version 

Get the current Version of the Project.

```
y7k version
y7k v 
```

### y7k bump 

Bump the current Version of the Project.

```
y7k bump [major|minor|patch]
y7k b [major|minor|patch]
```

## Roadmap

* Add 'Static' Type
* Add 'Kirby' (Once Version 2.4 is out)
* Add Commands to update Craft & Plugins
* Add Command to update Project

-----

Inspired by the [Kirby CLI](https://github.com/getkirby/cli)

