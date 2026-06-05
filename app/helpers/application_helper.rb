module ApplicationHelper
  # "about 3 hours ago", with the exact timestamp shown in a tooltip on hover.
  def time_ago_in_words_with_tooltip(time)
    return "" if time.blank?

    tag.span("#{time_ago_in_words(time)} ago", title: time.strftime("%d-%m-%Y %H:%M"))
  end
end
