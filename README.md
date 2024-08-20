# filterdom_php
this method return the content and change the &lt;img src=(base64) /> to &lt;img src="/storage_path/your_dir"

## Example Output
  Input
    <p>This is a sample content from WYSIWYG. An image is included on this content. <img src="base64 format image ............... " /></p>

  Output
    <p>This is a sample content from WYSIWYG. An image is included on this content. <img src="/your_path/path" /></p>
