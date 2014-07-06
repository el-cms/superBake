# Extend the shell

## Methods from SbShell

### speak()

<pre class="syntax brush-html">
&lt;?php
/**
 * Creates a pretty output
 *
 * If $decorations > 0, output will have an opening HR
 * If $decorations >= 2, output will have a closing HR
 *
 * @param mixed $text String to output, or array of strings.
 * @param string $class Class (info|warning|error|success|comment|bold)
 * @param integer $force 1 for Normal, 2 for Verbose only and 0 for Quiet shells. Use 4 for debugs (you must set $this->debug=1 somewhere)
 * @param integer $decorations If >0, text will be surrounded by hr and $decorations new lines.
 * @param integer $newLines Number of empty lines to insert before and after text.
 */
public function speak($text, $class = null, $force = 1, $decorations = 0, $newLines = 0) {
?&gt;
</pre>

Note that windows command line are always bi-colors only.

Examples:
<pre>
<strong>Class:</strong> error, <strong>decorations:</strong> 1, <strong>newLines:</strong> 0
<span class="text-danger">>>>>>>>>>>>>>>>>>>>>>>>>>>>[   ERROR   ]<<<<<<<<<<<<<<<<<<<<<<<
    o Message
      Can also be multiline
    o And an array of messages
</span>
<strong>Class:</strong> error, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
<span class="text-danger">>>>>>>>>>>>>>>>>>>>>>>>>>>>[   ERROR   ]<<<<<<<<<<<<<<<<<<<<<<<

    o Message
      Can also be multiline
    o And an array of messages

>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
</span>
<strong>Class:</strong> error, <strong>decorations:</strong> 0, <strong>newLines:</strong> 2,
<span class="text-danger">

    o Message
      Can also be multiline
    o And an array of messages

</span>
<strong>Class:</strong> warning, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
<span class="text-warning">/////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    # Message
      Can also be multiline
    # And an array of messages

\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\//////////////////////////////
</span>
<strong>Class:</strong> warning, <strong>decorations:</strong> 0, <strong>newLines:</strong> 1,
<span class="text-warning">
    # Message
      Can also be multiline
    # And an array of messages

</span>
<strong>Class:</strong> info, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
<span class="text-info">-[ I ]---------------------------------------------------------

    > Message
      Can also be multiline
    > And an array of messages

---------------------------------------------------------------
</span>
<strong>Class:</strong> info, <strong>decorations:</strong> 0, <strong>newLines:</strong> 1,
<span class="text-info">
    > Message
      Can also be multiline
    > And an array of messages

</span>
<strong>Class:</strong> success, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
<span class="text-success">`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_

    * Message
      Can also be multiline
    * And an array of messages

_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`\_/`
</span>
<strong>Class:</strong> success, <strong>decorations:</strong> 0, <strong>newLines:</strong> 1,
<span class="text-success">
    * Message
      Can also be multiline
    * And an array of messages

</span>
<strong>Class:</strong> comment, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
<span class="text-muted">* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

    - Message
      Can also be multiline
    - And an array of messages

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
</span>
<strong>Class:</strong> comment, <strong>decorations:</strong> 0, <strong>newLines:</strong> 1,
<span class="text-muted">
    - Message
      Can also be multiline
    - And an array of messages

</span>
<strong>Class:</strong> <em>null</em>, <strong>decorations:</strong> 2, <strong>newLines:</strong> 1,
---------------------------------------------------------------

    > Message
      Can also be multiline
    > And an array of messages

---------------------------------------------------------------

<strong>Class:</strong> <em>null</em>, <strong>decorations:</strong> 0, <strong>newLines:</strong> 1,
    > Message
      Can also be multiline
    > And an array of messages
</pre>

## Methods from Sbc

### displayLog()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the log array
 *
 * @return array
 */
public function displayLog() {}
?&gt;
</pre>

### getConfig()

<pre class="syntax brush-html">
&lt;?php
/**
 * Searches for the value of the given key in the config array.
 * Key must be in the format of "key.subKey.subSubKey", as for Configure::read()
 *
 * @param string $key
 * @return mixed Key's value
 */
public function getConfig($key = null) {}
?&gt;
</pre>

### getConfigPath()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the path to Console/Configurations/
 *
 * @return string Path to the configuration file
 */
public function getConfigPath() {}
?&gt;
</pre>

### getErrors()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the number of errors generated by the log() function.
 *
 * @return int
 */
public function getErrors() {}
?&gt;
</pre>

### getWarning()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns the number of warnings generated by the log() function.
 *
 * @return int
 */
	 public function getWarnings() {}
?&gt;
</pre>
### loadConfig()

<pre class="syntax brush-html">
&lt;?php
/**
 * Loads the configuration files and populates the array
 */
public function loadConfig() {}
?&gt;
</pre>

### log()

<pre class="syntax brush-html">
&lt;?php
/**
 * Logs a message in an array of messages.
 *
 * @param string $message The message
 * @param string $type Message type: info|warning|success|error
 * @param integer $level Level of the message. The lower the message is, the less it is important.
 */
public function log($message, $type = 'info', $level = 0) {}
?&gt;
</pre>

### populate()

<pre class="syntax brush-html">
&lt;?php
/**
 * Populates the configuration array with defaults values.
 */
public function populate() {}
?&gt;
</pre>

### prefixName()

<pre class="syntax brush-html">
&lt;?php
/**
 * Returns public if $prefix is null, or $prefix.
 * @param string $prefix Prefix to test
 * @return string
 */
public function prefixName($prefix) {}
?&gt;
</pre>

### updateArray()

<pre class="syntax brush-html">
&lt;?php
/**
 * Complete one array of default values with an array of defined values.
 * Default values are overwriten if in the defined array.
 * Keys from the defined array that are absent from the default array are added.
 *
 * @param array $default An array of default values
 * @param array $defined An array of defined values
 * @param boolean $keep If set to true, keep values defined in defined array and not in default array
 *                   will be kept and returned.
 * @return array Default array updated with defined array
 */
public function updateArray($default, $defined, $keep = false) {}
?&gt;
</pre>
