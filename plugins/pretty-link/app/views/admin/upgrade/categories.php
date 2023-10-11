<div id="col-container" class="wp-clearfix pretty-link-blur-wrap">
  <div class="pretty-link-blur">
    <div id="col-left">
      <div class="col-wrap">
        <div class="form-wrap">
          <h2>Add New Link Category</h2>
          <div class="form-field form-required term-name-wrap">
              <label for="tag-name">Name</label>
              <input
                type="text"
                value=""
                size="40"
              />
              <p>The name is how it appears on your site.</p>
            </div>
            <div class="form-field term-slug-wrap">
              <label for="tag-slug">Slug</label>
              <input name="slug" id="tag-slug" type="text" value="" size="40" />
              <p>
                The “slug” is the URL-friendly version of the name. It is usually
                all lowercase and contains only letters, numbers, and hyphens.
              </p>
            </div>
            <div class="form-field term-parent-wrap">
              <label for="parent">Parent Link Category</label>
              <select name="parent" id="parent" class="postform">
                <option value="-1" selected="selected">None</option>
              </select>
              <p>
                Assign a parent term to create a hierarchy. The term Jazz, for
                example, would be the parent of Bebop and Big Band.
              </p>
            </div>
            <div class="form-field term-description-wrap">
              <label for="tag-description">Description</label>
              <textarea
                name="description"
                id="tag-description"
                rows="5"
                cols="40"
              ></textarea>
              <p>
                The description is not prominent by default; however, some themes
                may show it.
              </p>
            </div>

            <p class="submit">
              <input
                type="submit"
                name="submit"
                id="submit"
                class="button button-primary"
                value="Add New Link Category"
              />
            </p>
        </div>
      </div>
    </div>
    <!-- /col-left -->

    <div id="col-right">
      <div class="col-wrap">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
              <label for="bulk-action-selector-top" class="screen-reader-text"
                >Select bulk action</label
              ><select name="action" id="bulk-action-selector-top">
                <option value="-1">Bulk Actions</option>
                <option value="delete">Delete</option>
              </select>
              <input
                type="submit"
                id="doaction"
                class="button action"
                value="Apply"
              />
            </div>
            <div class="tablenav-pages no-pages">
              <span class="displaying-num">0 items</span>
              <span class="pagination-links"
                ><span
                  class="tablenav-pages-navspan button disabled"
                  aria-hidden="true"
                  >«</span
                >
                <span
                  class="tablenav-pages-navspan button disabled"
                  aria-hidden="true"
                  >‹</span
                >
                <span class="paging-input"
                  ><label for="current-page-selector" class="screen-reader-text"
                    >Current Page</label
                  ><input
                    class="current-page"
                    id="current-page-selector"
                    type="text"
                    name="paged"
                    value="1"
                    size="1"
                    aria-describedby="table-paging"
                  /><span class="tablenav-paging-text">
                    of <span class="total-pages">0</span></span
                  ></span
                >
                <a
                  class="next-page button"
                  href=""
                  ><span class="screen-reader-text">Next page</span
                  ><span aria-hidden="true">›</span></a
                >
                <a
                  class="last-page button"
                  href=""
                  ><span class="screen-reader-text">Last page</span
                  ><span aria-hidden="true">»</span></a
                ></span
              >
            </div>
            <br class="clear" />
          </div>
          <h2 class="screen-reader-text">Categories list</h2>
          <table class="wp-list-table widefat fixed striped tags">
            <thead>
              <tr>
                <td id="cb" class="manage-column column-cb check-column">
                  <label class="screen-reader-text" for="cb-select-all-1"
                    >Select All</label
                  ><input id="cb-select-all-1" type="checkbox" />
                </td>
                <th
                  scope="col"
                  id="name"
                  class="manage-column column-name column-primary sortable desc"
                >
                  <a
                    href=""
                    ><span>Name</span><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th
                  scope="col"
                  id="description"
                  class="manage-column column-description sortable desc"
                >
                  <a
                    href=""
                    ><span>Description</span
                    ><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th
                  scope="col"
                  id="slug"
                  class="manage-column column-slug sortable desc"
                >
                  <a
                    href=""
                    ><span>Slug</span><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th
                  scope="col"
                  id="posts"
                  class="manage-column column-posts num sortable desc"
                >
                  <a
                    href=""
                    ><span>Count</span><span class="sorting-indicator"></span
                  ></a>
                </th>
              </tr>
            </thead>

            <tbody id="the-list" data-wp-lists="list:tag">
              <tr class="no-items">
                <td class="colspanchange" colspan="5">No categories found.</td>
              </tr>
            </tbody>

            <tfoot>
              <tr>
                <td class="manage-column column-cb check-column">
                  <label class="screen-reader-text" for="cb-select-all-2"
                    >Select All</label
                  ><input id="cb-select-all-2" type="checkbox" />
                </td>
                <th
                  scope="col"
                  class="manage-column column-name column-primary sortable desc"
                >
                  <a
                    href=""
                    ><span>Name</span><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th
                  scope="col"
                  class="manage-column column-description sortable desc"
                >
                  <a
                    href=""
                    ><span>Description</span
                    ><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th scope="col" class="manage-column column-slug sortable desc">
                  <a
                    href=""
                    ><span>Slug</span><span class="sorting-indicator"></span
                  ></a>
                </th>
                <th
                  scope="col"
                  class="manage-column column-posts num sortable desc"
                >
                  <a
                    href=""
                    ><span>Count</span><span class="sorting-indicator"></span
                  ></a>
                </th>
              </tr>
            </tfoot>
          </table>
          <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
              <label for="bulk-action-selector-bottom" class="screen-reader-text"
                >Select bulk action</label
              ><select name="action2" id="bulk-action-selector-bottom">
                <option value="-1">Bulk Actions</option>
                <option value="delete">Delete</option>
              </select>
              <input
                type="submit"
                id="doaction2"
                class="button action"
                value="Apply"
              />
            </div>
            <div class="tablenav-pages no-pages">
              <span class="displaying-num">0 items</span>
              <span class="pagination-links"
                ><span
                  class="tablenav-pages-navspan button disabled"
                  aria-hidden="true"
                  >«</span
                >
                <span
                  class="tablenav-pages-navspan button disabled"
                  aria-hidden="true"
                  >‹</span
                >
                <span class="screen-reader-text">Current Page</span
                ><span id="table-paging" class="paging-input"
                  ><span class="tablenav-paging-text"
                    >1 of <span class="total-pages">0</span></span
                  ></span
                >
                <a
                  class="next-page button"
                  href=""
                  ><span class="screen-reader-text">Next page</span
                  ><span aria-hidden="true">›</span></a
                >
                <a
                  class="last-page button"
                  href=""
                  ><span class="screen-reader-text">Last page</span
                  ><span aria-hidden="true">»</span></a
                ></span
              >
            </div>
            <br class="clear" />
          </div>
      </div>
    </div>
    <!-- /col-right -->
  </div>
  <?php include_once PRLI_VIEWS_PATH . "/admin/upgrade/dialog.php"; ?>
</div>
