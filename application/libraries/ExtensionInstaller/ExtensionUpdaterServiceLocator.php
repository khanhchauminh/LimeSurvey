<?php

/**
 * LimeSurvey
 * Copyright (C) 2007-2015 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

namespace LimeSurvey\ExtensionInstaller;

/**
 * @since 2018-09-26
 * @author Olle Haerstedt
 */
class ExtensionUpdaterServiceLocator
{
    /**
     * @var array<string, callable>
     */
    protected $updaters = [];

    /**
     * All Yii components need an init() method.
     * @return void
     */
    public function init() : void
    {
        $this->addUpdater(
            'plugin',
            function () {
                return PluginUpdater::createUpdaters();
            }
        );
    }

    /**
     * @param string $name Updater class name, like 'PluginUpdater', or 'ExtensionUpdater'.
     * @param callable $creator Callable that returns an ExtensionUpdater array.
     * @return void
     */
    public function addUpdater(string $name, callable $creator) : void
    {
        if (isset($this->updaters[$name])) {
            throw new \Exception("Extension installer with name $name already exists");
        }
        $this->updaters[$name] = $creator;
    }

    /**
     * @param string $name
     * @return ExtensionUpdater
     */
    public function getUpdater(string $name) : ?ExtensionUpdater
    {
        if (isset($this->updaters[$name])) {
            $updater =  $this->updaters[$name]();
            return $updater;
        } else {
            return null;
        }
    }

    /**
     * @return ExtensionUpdater[]
     */
    public function getAllUpdaters()
    {
        // Get an extension updater for each extension installed.
        $updaters = [];
        $errors = [];
        foreach ($this->updaters as $name => $creator) {
            $updaters = array_merge($creator(), $updaters);
        }
        return $updaters;
    }
}
