<div class="pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            Export Pretty Links
            <span id="prli-tooltip-plp-export-links" class="prli-tooltip">
              <span><i class="pl-icon pl-icon-info-circled pl-16"></i></span>
              <span class="prli-data-title prli-hidden">Export Pretty Links</span>
              <span class="prli-data-info prli-hidden">Export Links to a CSV File</span>
            </span>
          </th>
          <td>
            <a href="#" class="button button-primary">Export</a>
          </td>
        </tr>
        <tr>
          <th scope="row">
            Import Pretty Links
            <span id="prli-tooltip-plp-import-links" class="prli-tooltip">
              <span><i class="pl-icon pl-icon-info-circled pl-16"></i></span>
              <span class="prli-data-title prli-hidden">Import Pretty Links</span>
              <span class="prli-data-info prli-hidden">There are two ways to import a file.<br /><br />1) Importing to
                update existing links and<br /><br />2) Importing to generate new
                links. When Importing to generate new links, you must delete the
                "id" column from the CSV before importing. If the "id" column is
                present, Pretty Links Pro will attempt to update existing
                links.</span>
            </span>
          </th>
          <td>
            <input name="importedfile" type="file" />
            <br />
            <input type="submit" class="button button-primary" value="Import" />
            <span id="prli-tooltip-plp-import-links-select-file" class="prli-tooltip">
              <span><i class="pl-icon pl-icon-info-circled pl-16"></i></span>
              <span class="prli-data-title prli-hidden">Links Import File</span>
              <span class="prli-data-info prli-hidden">Select a file that has been formatted as a Pretty Link CSV import file and click "Import"</span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php include_once PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php"; ?>
</div>