---
slug: dataliberation
title: DataLiberation
install: wp-php-toolkit/data-liberation

see_also:
  - ../learn/03-importing-content.html | Tutorial — Markdown to WXR | The chapter that walks through importing a folder of Markdown files into WordPress via the toolkit.
  - markdown | Markdown | Use Markdown as a source or destination format.
  - blockparser | BlockParser | Analyze serialized blocks inside post content.
  - httpclient | HttpClient | Download media and remote source data while importing.
---

Streaming WordPress import/export. WXR, SQL, block markup — process entities one at a time instead of building whole-dataset object graphs.

## Why this exists

<p>WordPress content should be portable, but real migrations cross several formats. A site export might arrive as WXR, a Markdown folder, or entities from another CMS. URLs can hide in block attributes, HTML, CSS, feeds, GUIDs, and post meta. Importers must also resume after a failed media download or upload.</p>

<p>The DataLiberation component streams WordPress-shaped data through readers, transformers, and writers. It models posts, terms, comments, attachments, and metadata as <code>ImportEntity</code> objects, then lets a pipeline rewrite each entity without loading the full export into memory.</p>

<p>The API reflects specific migration bugs: relative URLs in known block attributes, URLs inside inline CSS, self-closing block comments that must keep their shape, and origin-only URLs whose trailing slash style should not change during a rewrite.</p>

<p>Reach for it when the job combines formats: build WXR from another CMS, rewrite a staging export for production, frontload remote assets, or compose Markdown, XML, HTML, CSS, and URL rewriting into one pipeline.</p>

## Write a WXR file in five lines

<p>Stream a single post into a WXR document via <code>WXRWriter</code>. The writer emits each entity to the output stream and only keeps the small amount of state needed for the current document.</p>

<!-- snippet:
filename: wxr-quickstart.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\ByteStream\MemoryPipe;
use WordPress\DataLiberation\EntityWriter\WXRWriter;
use WordPress\DataLiberation\ImportEntity;

$pipe   = new MemoryPipe();
$writer = new WXRWriter( $pipe );
$writer->append_entity( new ImportEntity( 'post', array(
	'post_title' => 'Hello',
	'content'    => 'World.',
	'post_id'    => '1',
	'status'     => 'publish',
) ) );
$writer->finalize();
$writer->close_writing();
$pipe->close_writing();
$wxr = $pipe->consume_all();

echo "bytes: " . strlen( $wxr ) . "\n";
echo false !== strpos( $wxr, '<title>Hello</title>' ) ? "title exported\n" : "title missing\n";
echo false !== strpos( $wxr, '<wp:status>publish</wp:status>' ) ? "status exported\n" : "status missing\n";
```

<!-- expected-output -->
```
bytes: 475
title exported
status exported
```

## Build a WXR programmatically from any source

<p>The writer doesn't care where entities come from. Loop over rows from a CMS, a CSV, or a Notion API dump and emit posts plus their meta and comments.</p>

<!-- snippet:
filename: build-wxr.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\ByteStream\MemoryPipe;
use WordPress\DataLiberation\EntityWriter\WXRWriter;
use WordPress\DataLiberation\ImportEntity;

$rows = array(
	array( 'id' => 10, 'title' => 'About', 'body' => '<p>About us.</p>', 'tags' => array( 'company' ) ),
	array( 'id' => 11, 'title' => 'Blog',  'body' => '<p>Hello world.</p>', 'tags' => array( 'news', 'launch' ) ),
);

$pipe   = new MemoryPipe();
$writer = new WXRWriter( $pipe );

foreach ( $rows as $row ) {
	$writer->append_entity( new ImportEntity( 'post', array(
		'post_id'    => (string) $row['id'],
		'post_title' => $row['title'],
		'content'    => $row['body'],
		'status'     => 'publish',
		'post_type'  => 'post',
	) ) );
	foreach ( $row['tags'] as $i => $tag ) {
		$writer->append_entity( new ImportEntity( 'term', array(
			'term_id'  => (string) ( $row['id'] * 100 + $i ),
			'taxonomy' => 'post_tag',
			'slug'     => $tag,
			'parent'   => '0',
		) ) );
	}
}

$writer->finalize();
$writer->close_writing();
$pipe->close_writing();

$wxr = $pipe->consume_all();
echo "items: " . substr_count( $wxr, '<item>' ) . "\n";
echo "terms: " . substr_count( $wxr, '<wp:term>' ) . "\n";
echo false !== strpos( $wxr, '<title>Blog</title>' ) ? "Blog post exported\n" : "Blog post missing\n";
```

<!-- expected-output -->
```
items: 2
terms: 3
Blog post exported
```

## Read entities from a WXR file incrementally

<p><code>WXREntityReader</code> emits one entity at a time. Memory use is driven by the current entity and parser buffers rather than the total file size.</p>

<!-- snippet:
filename: wxr-read.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\DataLiberation\EntityReader\WXREntityReader;

$wxr = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:wp="http://wordpress.org/export/1.2/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<title>Demo</title>
<item><title>First</title><wp:post_id>1</wp:post_id><wp:post_type>post</wp:post_type><content:encoded>Body 1</content:encoded></item>
<item><title>Second</title><wp:post_id>2</wp:post_id><wp:post_type>post</wp:post_type><content:encoded>Body 2</content:encoded></item>
</channel>
</rss>
XML;

$reader = WXREntityReader::create();
$reader->append_bytes( $wxr );
$reader->input_finished();

while ( $reader->next_entity() ) {
	$entity = $reader->get_entity();
	echo $entity->get_type() . ': ' . json_encode( $entity->get_data() ) . "\n";
}
```

<!-- expected-output -->
```
site_option: {"option_name":"blogname","option_value":"Demo"}
post: {"post_title":"First","post_id":"1","post_type":"post","post_content":"Body 1"}
post: {"post_title":"Second","post_id":"2","post_type":"post","post_content":"Body 2"}
```

## Streaming transform: rewrite URLs while copying WXR

<p>Wire reader to writer to rewrite a WXR file on the fly. This pattern is how you migrate a staging export to production: swap <code>staging.example.com</code> for <code>example.com</code> while holding only the current entity and output buffers.</p>

<!-- snippet:
filename: rewrite-urls.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\ByteStream\MemoryPipe;
use WordPress\DataLiberation\EntityReader\WXREntityReader;
use WordPress\DataLiberation\EntityWriter\WXRWriter;
use WordPress\DataLiberation\ImportEntity;

$source_xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:wp="http://wordpress.org/export/1.2/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<item><title>Hello</title><wp:post_id>1</wp:post_id><wp:post_type>post</wp:post_type>
<content:encoded>Visit https://staging.example.com/about for more.</content:encoded></item>
</channel>
</rss>
XML;

$reader = WXREntityReader::create();
$reader->append_bytes( $source_xml );
$reader->input_finished();

$out_pipe = new MemoryPipe();
$writer   = new WXRWriter( $out_pipe );

while ( $reader->next_entity() ) {
	$entity = $reader->get_entity();
	$data   = $entity->get_data();
	foreach ( array( 'post_content', 'content', 'description' ) as $field ) {
		if ( isset( $data[ $field ] ) ) {
			$data[ $field ] = str_replace( 'staging.example.com', 'example.com', $data[ $field ] );
		}
	}
	if ( 'post' === $entity->get_type() ) {
		$data['content'] = isset( $data['post_content'] ) ? $data['post_content'] : ( isset( $data['content'] ) ? $data['content'] : '' );
	}
	$writer->append_entity( new ImportEntity( $entity->get_type(), $data ) );
}

$writer->finalize();
$writer->close_writing();
$out_pipe->close_writing();

$wxr = $out_pipe->consume_all();
echo false !== strpos( $wxr, 'https://example.com/about' ) ? "new URL present\n" : "new URL missing\n";
echo false === strpos( $wxr, 'staging.example.com' ) ? "old URL removed\n" : "old URL still present\n";
```

<!-- expected-output -->
```
new URL present
old URL removed
```

## Render Markdown into a WXR import in one pipeline

<p>Compose <code>MarkdownConsumer</code> with <code>WXRWriter</code> to publish a folder of Markdown directly as a WordPress import file.</p>

<!-- snippet:
filename: md-to-wxr.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\ByteStream\MemoryPipe;
use WordPress\DataLiberation\EntityWriter\WXRWriter;
use WordPress\DataLiberation\ImportEntity;
use WordPress\Markdown\MarkdownConsumer;

@mkdir( '/tmp/md-src', 0777, true );
file_put_contents( '/tmp/md-src/hello.md',  "---\ntitle: Hello\n---\n\n# Hello\n\nFirst post." );
file_put_contents( '/tmp/md-src/second.md', "---\ntitle: Second\n---\n\nMore text **here**." );

$pipe   = new MemoryPipe();
$writer = new WXRWriter( $pipe );

$id = 1;
foreach ( glob( '/tmp/md-src/*.md' ) as $path ) {
	$consumer = new MarkdownConsumer( file_get_contents( $path ) );
	$consumer->consume();
	$writer->append_entity( new ImportEntity( 'post', array(
		'post_id'    => (string) $id++,
		'post_title' => $consumer->get_meta_value( 'title' ) ?: basename( $path, '.md' ),
		'content'    => $consumer->get_block_markup(),
		'status'     => 'publish',
		'post_type'  => 'post',
		'post_name'  => basename( $path, '.md' ),
	) ) );
}

$writer->finalize();
$writer->close_writing();
$pipe->close_writing();

$wxr = $pipe->consume_all();
echo "posts: " . substr_count( $wxr, '<item>' ) . "\n";
echo false !== strpos( $wxr, '&lt;!-- wp:heading' ) ? "block markup exported\n" : "block markup missing\n";
echo false !== strpos( $wxr, '<title>Second</title>' ) ? "frontmatter title exported\n" : "frontmatter title missing\n";
```

<!-- expected-output -->
```
posts: 2
block markup exported
frontmatter title exported
```

## Replace a CSS value prefix without changing its suffix

`CSSProcessor::measure_value_prefix()` finds how many source bytes represent a
decoded prefix, including CSS escapes and string line continuations. Replace
those bytes with `escape_value_prefix()` output to keep the existing quotes,
`url()` wrapper, and unmatched suffix unchanged. The escaped replacement also
works in unquoted URLs. Whole-value `set_token_value()` still adds quotes and
normalizes CRLF to one newline without dropping text after a lone CR.

Prefix measurement scans ordinary URL bytes with `strspn()` and decodes escapes
and UTF-8 separately. Replacement escaping uses `strtr()` rather than a PHP
character loop, with an early return when no bytes need escaping.

[The prefix-edit caller](Tests/fixtures/css-prefix/replace-url-prefix.php) and
[the whole-URL caller](Tests/fixtures/css-prefix/replace-whole-url.php) show each
operation separately, without streamed input or saved cursors.

## Find CSS URLs in imports and image sets

`CSSURLProcessor::next_url()` recognizes `url()`, bare `@import` strings, and
strings used as images in `image-set()` or `-webkit-image-set()`. Comments,
displayed text, MIME-type strings, and malformed string or URL tokens are
not returned. More than 128 nested image sets throw an error.

[The file-rewrite caller](Tests/fixtures/css-context/rewrite-file.php) uses the
whole-string iterator and writes the output only after iteration completes.

## Stream CSS with the existing scan-and-edit API

For example, one read can end at `url(https://old.exa` and the next can supply
`mple/photo.png)`. The processor waits for the rest of that URL before returning
it. The caller then reads and edits it with the same methods used for a complete
CSS string.

Both `CSSProcessor` and `CSSURLProcessor` follow the XML streaming API:

1. Call `create_for_streaming($css = '', $cursor = null)` to supply initial
   bytes or start with an empty buffer.
2. Call `append_bytes($bytes)` when more source bytes arrive.
3. Use `next_token()` or `next_url()` and the existing getters and setters.
   A false result can mean the processor needs more input. Check
   `is_paused_at_incomplete_input()` to distinguish that from completion.
4. Call `input_finished()` only at the actual source end. Continue scanning
   to read the last tokens. A stopped download is not the source end.
5. `is_finished()` becomes true when input has ended and no current or unread
   token remains. `is_expecting_more_input()` says whether more bytes can still
   be appended.

The whole-string API is unchanged: `CSSProcessor::create($css)` and
`new CSSURLProcessor($css)` still take a complete stylesheet. Getters, setters,
and `get_updated_css()` work the same way with either input mode. There is no
separate `rewrite_chunk()` API or built-in URL mapping policy.

<!-- snippet:
filename: css-chunks.php
runnable: true
-->
```php
<?php
require '/php-toolkit/vendor/autoload.php';

use WordPress\DataLiberation\URL\CSSURLProcessor;

$processor = CSSURLProcessor::create_for_streaming();
$parts = array( 'a{src:url(https://old.exa', 'mple/photo.png)}' );
foreach ( $parts as $index => $bytes ) {
	$processor->append_bytes( $bytes );
	if ( $index === count( $parts ) - 1 ) {
		$processor->input_finished();
	}
	while ( $processor->next_url() ) {
		if ( 'https://old.example/photo.png' === $processor->get_raw_url() ) {
			$processor->set_raw_url( 'https://new.example/photo.png' );
		}
	}
	echo $processor->flush_processed_css();
}
echo "\n";
```

<!-- expected-output -->
```
a{src:url("https://new.example/photo.png")}
```

The caller chooses which URLs to change. `set_raw_url()` uses the existing
whole-value setter: it quotes an unquoted URL and escapes the replacement for
CSS. For example, replacing `old.png` with `new.png` changes `url(old.png)` to
`url("new.png")`. It does not apply a separate prefix-only rewrite rule.

### Write and release completed output

`flush_processed_css()` returns completed CSS with edits applied and removes
those source bytes from memory. An unfinished token remains for the next read.
Flushing clears the current token or URL, so edit it before flushing.

Appending input does not require a flush. Without flushing, `get_updated_css()`
returns all supplied CSS with edits applied. After a flush, it returns only the
retained CSS. Choose when to flush based on how much output the caller wants to
keep in memory. For example, flushing after each edited URL avoids storing many
large replacements. Flush once more after scanning stops to get completed CSS
after the last URL. There is no automatic output-size threshold or byte slicing.

There is no token-size cap. A large comment, string, identifier, or embedded
image can use a lot of memory even with small input chunks. Each read reparses
the unfinished token. Small chunks therefore do not bound the largest token's
memory use or parsing work. The cursor does not copy these bytes.
More than 128 open, nested `image-set()` functions causes an error.

### Resume in a new process

Suppose the source is `a{src:url(https://old.example/photo.png)}`. A read ends
inside the URL. After flushing `a{src:`, the saved source offset points at
`url(`, not at the end of that read. A new process reads the unfinished URL
again from `url(`. It does not repeat the flushed prefix.

`get_reentrancy_cursor()` returns an opaque string. Save it with
`get_token_byte_offset_in_the_input_stream()`. Supply source bytes from that
offset to `create_for_streaming($css, $cursor)`. The cursor contains parsing
state, including the URL position after `@import` or inside `image-set()`, but
no source bytes or edits. Do not inspect or change its internal format.

A cursor saved while a token or URL is current reads that token again on
resume, as XML does. If `input_finished()` was already called, pass all remaining
source bytes to the factory; appending after the source end is rejected.
For file rewrites, save a checkpoint after flushing completed output instead:

1. Read a source chunk and append it. Mark the source end when it is reached.
2. Scan and edit using the ordinary token or URL methods.
3. Write the string from `flush_processed_css()` and flush the output file.
4. Save the processor's source byte offset, the output file offset, and the
   parser cursor together. Do not use the input file handle's current offset:
   it can be past bytes that the processor still needs to read again.

On resume, seek the source to its saved offset. Remove output bytes after the
saved output offset, then append there. Those extra bytes may have been written
before the previous process stopped, but after its last checkpoint. Removing
them prevents duplicate output when the corresponding source is read again.
Keep the source file and the caller's edit rules unchanged between runs.
The processor does not check either of them.

If a write fails, discard the processor and resume from the last checkpoint.
The [token file caller](Tests/fixtures/css-token-stream/rewrite-file.php) and
[URL file caller](Tests/fixtures/css-stream/rewrite-file.php) show how to save
and restore both file positions and the parser state. Their tests stop on both
sides of a checkpoint and start a fresh PHP process to finish the output.
