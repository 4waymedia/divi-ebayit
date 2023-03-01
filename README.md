This project was bootstrapped with [Create Divi Extension](https://github.com/elegantthemes/create-divi-extension).

Below you will find some information on how to perform common tasks. You can find the most recent version of this guide [here](https://github.com/elegantthemes/create-divi-extension/blob/master/packages/divi-scripts/template/README.md).



## Just use the extension

If you want to just install and use the extension, you can do so by either of the following methods. You will find zip file in the versions folder. I don't plan on keeping this up-to-date or maintaining a thorough update log.

1) Upload via FTP or directly into your wordpress plugins folder

## Divi extension open code source

Create Divi Extension is divided into two packages:

* `create-divi-extension` is a global command-line utility that you use to create new projects.
* `divi-scripts` is a development dependency in the generated projects (including this one).

You almost never need to update `create-divi-extension` itself: it delegates all the setup to `divi-scripts`.

When you run `create-divi-extension`, it always creates the project with the latest version of `divi-scripts` so you’ll get all the new features and improvements in newly created extensions automatically.

To update an existing project to a new version of `divi-scripts`, [open the changelog](https://github.com/elegantthemes/create-divi-extension/blob/master/CHANGELOG.md), find the version you’re currently on (check `package.json` in this folder if you’re not sure), and apply the migration instructions for the newer versions.

In most cases bumping the `divi-scripts` version in `package.json` and running `npm install` in this folder should be enough, but it’s good to consult the [changelog](https://github.com/elegantthemes/create-divi-extension/blob/master/CHANGELOG.md) for potential breaking changes.

We commit to keeping the breaking changes minimal so you can upgrade `divi-scripts` painlessly.

## Sending Feedback

We are always open to [your feedback](https://github.com/elegantthemes/create-divi-extension/issues).

## Folder Structure

If you setup the project, your project should look like this:

```
my-extension
├── includes
│   ├── modules
│   │   └── HelloWorld
│   │       ├── HelloWorld.jsx
│   │       ├── HelloWorld.php
│   │       └── style.css
│   ├── loader.js
│   ├── loader.php
│   └── MyExtension.php
├── languages
├── node_modules
├── scripts
│   └── frontend.js
├── styles
├── my-extension.php
├── package.json
└── README.md
└── versions
│   ├── version-1.x.zip
│   ├── version-2.x.zip
```

For info on how to build your own extension after making modifications, see the readme-buildit.md
